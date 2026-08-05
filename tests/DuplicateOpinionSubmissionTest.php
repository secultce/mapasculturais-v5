<?php

require_once __DIR__ . '/bootstrap.php';
// Work around the legacy test autoloader not registering module entity subclasses.
require_once __DIR__ . '/../src/protected/application/lib/modules/Diligence/Entities/DiligenceFile.php';

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class DuplicateOpinionSubmissionTest extends MapasCulturais_TestCase
{
    public function testRegistrationEvaluationIsUniquePerRegistrationAndUser()
    {
        $connection = $this->app->em->getConnection();
        $registration = $this->createRegistrationRow();
        $userId = $connection->fetchColumn('SELECT id FROM usr ORDER BY id LIMIT 1');

        $this->assertNotFalse($userId, 'The test database must contain a user');

        $parameters = [
            $registration['registration_id'],
            $userId,
            '{}',
        ];

        $connection->executeUpdate(
            "INSERT INTO registration_evaluation
                (id, registration_id, user_id, evaluation_data, status, create_timestamp)
             VALUES
                (nextval('registration_evaluation_id_seq'), ?, ?, ?, 0, NOW())",
            $parameters
        );

        $this->expectException(UniqueConstraintViolationException::class);

        $connection->executeUpdate(
            "INSERT INTO registration_evaluation
                (id, registration_id, user_id, evaluation_data, status, create_timestamp)
             VALUES
                (nextval('registration_evaluation_id_seq'), ?, ?, ?, 0, NOW())",
            $parameters
        );
    }

    public function testOpinionIsUniquePerRegistration()
    {
        $connection = $this->app->em->getConnection();
        $registration = $this->createRegistrationRow();

        $parameters = [
            'Regression test opinion',
            $registration['registration_id'],
            $registration['agent_id'],
        ];

        $connection->executeUpdate(
            "INSERT INTO accountability_opinion
                (id, opinion, status, registration_id, agent_id, create_timestamp)
             VALUES
                (nextval('accountability_opinion_id_seq'), ?, 0, ?, ?, NOW())",
            $parameters
        );

        $this->expectException(UniqueConstraintViolationException::class);

        $connection->executeUpdate(
            "INSERT INTO accountability_opinion
                (id, opinion, status, registration_id, agent_id, create_timestamp)
             VALUES
                (nextval('accountability_opinion_id_seq'), ?, 0, ?, ?, NOW())",
            $parameters
        );
    }

    public function testConcurrentEvaluationInitializationIsSerialized()
    {
        $this->requireSubprocessSupport();

        $database = $this->createIndependentConnection();
        $fixture = $this->createCommittedRegistrationFixture($database);
        $tag = 'evaluation_race_' . bin2hex(random_bytes(6));
        $blocker = $this->createIndependentConnection();
        $workers = [];

        try {
            $blocker->beginTransaction();
            $statement = $blocker->prepare('SELECT id FROM registration WHERE id = ? FOR UPDATE');
            $statement->execute([$fixture['registration_id']]);

            $workerCode = $this->createEvaluationWorkerCode(
                $fixture['registration_id'],
                $fixture['user_id'],
                $tag
            );
            $workers[] = $this->startWorker($workerCode);
            $workers[] = $this->startWorker($workerCode);

            $this->waitForWorkersToBlock($database, $tag, 2, $workers);
            $blocker->commit();

            foreach ($workers as &$worker) {
                $this->assertWorkerSucceeded($worker);
            }
            unset($worker);

            $statement = $database->prepare(
                'SELECT COUNT(*), COUNT(DISTINCT id)
                 FROM registration_evaluation
                 WHERE registration_id = ? AND user_id = ?'
            );
            $statement->execute([
                $fixture['registration_id'],
                $fixture['user_id'],
            ]);

            $this->assertSame(
                [1, 1],
                $statement->fetch(\PDO::FETCH_NUM),
                'Both requests must return successfully while creating only one evaluation'
            );
        } finally {
            if ($blocker->inTransaction()) {
                $blocker->rollBack();
            }
            $this->terminateWorkers($workers);
            $this->deleteCommittedRegistrationFixture($database, $fixture);
        }
    }

    public function testConcurrentOpinionCreationIsSerialized()
    {
        $this->requireSubprocessSupport();

        $database = $this->createIndependentConnection();
        $fixture = $this->createCommittedRegistrationFixture($database);
        $tag = 'opinion_race_' . bin2hex(random_bytes(6));
        $blocker = $this->createIndependentConnection();
        $workers = [];

        try {
            $blocker->beginTransaction();
            $statement = $blocker->prepare('SELECT id FROM registration WHERE id = ? FOR UPDATE');
            $statement->execute([$fixture['registration_id']]);

            $workerCode = $this->createOpinionWorkerCode(
                $fixture['registration_id'],
                $fixture['user_id'],
                $tag
            );
            $workers[] = $this->startWorker($workerCode);
            $workers[] = $this->startWorker($workerCode);

            $this->waitForWorkersToBlock($database, $tag, 2, $workers);
            $blocker->commit();

            foreach ($workers as &$worker) {
                $this->assertWorkerSucceeded($worker);
            }
            unset($worker);

            $statement = $database->prepare(
                'SELECT COUNT(*), COUNT(DISTINCT id)
                 FROM accountability_opinion
                 WHERE registration_id = ?'
            );
            $statement->execute([$fixture['registration_id']]);

            $this->assertSame(
                [1, 1],
                $statement->fetch(\PDO::FETCH_NUM),
                'Both requests must return successfully while creating only one opinion'
            );
        } finally {
            if ($blocker->inTransaction()) {
                $blocker->rollBack();
            }
            $this->terminateWorkers($workers);
            $this->deleteCommittedRegistrationFixture($database, $fixture);
            @unlink('/tmp/mapasculturais-tests-authenticated-user.id');
        }
    }

    public function testSentEvaluationNoOpStillRejectsUnauthorizedUser()
    {
        $connection = $this->app->em->getConnection();
        $fixture = $this->createCurrentTransactionRegistrationFixture();
        $evaluationOwner = $this->getUser('normal', 0);
        $unauthorizedUser = $this->getUser('normal', 1);
        $confidentialData = ['status' => 10, 'confidential' => 'owner-only'];

        $connection->executeUpdate(
            "INSERT INTO registration_evaluation
                (id, registration_id, user_id, evaluation_data, status, create_timestamp)
             VALUES
                (nextval('registration_evaluation_id_seq'), ?, ?, ?, ?, NOW())",
            [
                $fixture['registration_id'],
                $evaluationOwner->id,
                json_encode($confidentialData),
                \MapasCulturais\Entities\RegistrationEvaluation::STATUS_SENT,
            ]
        );

        $registration = $this->app->repo('Registration')->find($fixture['registration_id']);
        $returnedEvaluation = null;
        $exception = null;

        $this->app->auth->logout();
        $this->app->auth->setAuthenticatedUser($unauthorizedUser);

        try {
            $returnedEvaluation = $registration->saveUserEvaluation(
                $confidentialData,
                $evaluationOwner,
                \MapasCulturais\Entities\RegistrationEvaluation::STATUS_SENT
            );
        } catch (\Throwable $caught) {
            $exception = $caught;
        } finally {
            $this->app->auth->logout();
        }

        $this->assertInstanceOf(
            \MapasCulturais\Exceptions\PermissionDenied::class,
            $exception,
            'A sent-evaluation no-op must perform authorization before returning the entity'
        );
        $this->assertNull(
            $returnedEvaluation,
            'An unauthorized caller must never receive an evaluation containing evaluationData'
        );
    }

    public function testAccountabilityEvaluationIsGlobalAcrossUsers()
    {
        $connection = $this->app->em->getConnection();
        $fixture = $this->createCurrentTransactionRegistrationFixture(true);
        $firstUser = $this->getUser('normal', 0);
        $secondUser = $this->getUser('normal', 1);
        $registration = $this->app->repo('Registration')->find($fixture['registration_id']);

        $this->app->disableAccessControl();

        try {
            $firstEvaluation = $registration->saveUserEvaluation(
                ['result' => 10, 'obs' => 'First accountability opinion'],
                $firstUser,
                \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED
            );
            $secondEvaluation = $registration->saveUserEvaluation(
                ['result' => 8, 'obs' => 'Second accountability opinion'],
                $secondUser,
                \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED
            );
        } finally {
            $this->app->enableAccessControl();
        }

        $this->assertSame(
            $firstEvaluation->id,
            $secondEvaluation->id,
            'Accountability has one technical evaluation for the registration, not one per user'
        );
        $this->assertSame(
            1,
            (int) $connection->fetchColumn(
                'SELECT COUNT(*) FROM registration_evaluation WHERE registration_id = ?',
                [$fixture['registration_id']]
            )
        );
    }

    public function testSaveUserEvaluationAndLegacySaveDoNotDeadlock()
    {
        $this->requireSubprocessSupport();

        $database = $this->createIndependentConnection();
        $fixture = $this->createCommittedRegistrationFixture($database);
        $evaluationId = $this->createCommittedEvaluation($database, $fixture);
        $legacyTag = 'legacy_eval_race_' . bin2hex(random_bytes(6));
        $saveTag = 'save_eval_race_' . bin2hex(random_bytes(6));
        $blocker = $this->createIndependentConnection();
        $workers = [];

        try {
            $blocker->beginTransaction();
            $statement = $blocker->prepare(
                'SELECT id FROM registration_evaluation WHERE id = ? FOR UPDATE'
            );
            $statement->execute([$evaluationId]);

            $workers[] = $this->startWorker($this->createLegacyEvaluationSaveWorkerCode(
                $evaluationId,
                $legacyTag
            ));
            $this->waitForTaggedWorkersToWait($database, [$legacyTag], $workers);

            $workers[] = $this->startWorker($this->createSaveUserEvaluationWorkerCode(
                $fixture['registration_id'],
                $fixture['user_id'],
                $saveTag
            ));
            $this->waitForTaggedWorkersToWait(
                $database,
                [$legacyTag, $saveTag],
                $workers
            );

            $blocker->commit();

            foreach ($workers as &$worker) {
                $this->assertWorkerSucceeded($worker);
            }
            unset($worker);

            $statement = $database->prepare(
                'SELECT COUNT(*) FROM registration_evaluation WHERE registration_id = ?'
            );
            $statement->execute([$fixture['registration_id']]);
            $this->assertSame(1, (int) $statement->fetchColumn());
        } finally {
            if ($blocker->inTransaction()) {
                $blocker->rollBack();
            }
            $this->terminateWorkers($workers);
            $this->deleteCommittedRegistrationFixture($database, $fixture);
        }
    }

    public function testDeleteAndSaveUserEvaluationDoNotDeadlockAndRecreateEvaluation()
    {
        $this->requireSubprocessSupport();

        $database = $this->createIndependentConnection();
        $fixture = $this->createCommittedRegistrationFixture($database);
        $evaluationId = $this->createCommittedEvaluation($database, $fixture);
        $fixture['removed_evaluation_ids'] = [$evaluationId];
        $deleteTag = 'delete_eval_race_' . bin2hex(random_bytes(6));
        $saveTag = 'save_after_delete_race_' . bin2hex(random_bytes(6));
        $blocker = $this->createIndependentConnection();
        $workers = [];

        try {
            $blocker->beginTransaction();
            $statement = $blocker->prepare(
                'SELECT id FROM registration_evaluation WHERE id = ? FOR UPDATE'
            );
            $statement->execute([$evaluationId]);

            $workers[] = $this->startWorker($this->createEvaluationDeleteWorkerCode(
                $evaluationId,
                $deleteTag
            ));
            $this->waitForTaggedWorkersToWait($database, [$deleteTag], $workers);

            $workers[] = $this->startWorker($this->createSaveUserEvaluationWorkerCode(
                $fixture['registration_id'],
                $fixture['user_id'],
                $saveTag
            ));
            $this->waitForTaggedWorkersToWait(
                $database,
                [$deleteTag, $saveTag],
                $workers
            );

            $blocker->commit();

            foreach ($workers as &$worker) {
                $this->assertWorkerSucceeded($worker);
            }
            unset($worker);

            $statement = $database->prepare(
                "SELECT id, user_id, status, evaluation_data::json->>'status' AS data_status
                 FROM registration_evaluation
                 WHERE registration_id = ?"
            );
            $statement->execute([$fixture['registration_id']]);
            $evaluations = $statement->fetchAll();

            $this->assertCount(
                1,
                $evaluations,
                'The save queued after delete must recreate exactly one evaluation'
            );
            $this->assertNotSame($evaluationId, (int) $evaluations[0]['id']);
            $this->assertSame($fixture['user_id'], (int) $evaluations[0]['user_id']);
            $this->assertSame(
                \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED,
                (int) $evaluations[0]['status']
            );
            $this->assertSame('8', $evaluations[0]['data_status']);
        } finally {
            if ($blocker->inTransaction()) {
                $blocker->rollBack();
            }
            $this->terminateWorkers($workers);
            $this->deleteCommittedRegistrationFixture($database, $fixture);
        }
    }

    private function createRegistrationRow()
    {
        $connection = $this->app->em->getConnection();
        $project = $connection->fetchAssoc(
            'SELECT id AS project_id, agent_id
             FROM project
             WHERE agent_id IS NOT NULL
             ORDER BY id
             LIMIT 1'
        );

        $this->assertNotFalse($project, 'The test database must contain a project with an owner');

        $opportunityId = $connection->fetchColumn(
            "INSERT INTO opportunity
                (agent_id, type, name, short_description, published_registrations,
                 create_timestamp, status, object_type, object_id)
             VALUES
                (?, 1, 'Duplicate submission regression test', 'Regression test', FALSE,
                 NOW(), 1, 'MapasCulturais\\Entities\\Project', ?)
             RETURNING id",
            [
                $project['agent_id'],
                $project['project_id'],
            ]
        );

        $registrationId = $connection->fetchColumn(
            "INSERT INTO registration
                (opportunity_id, agent_id, create_timestamp, status, agents_data)
             VALUES
                (?, ?, NOW(), 0, '{}')
             RETURNING id",
            [
                $opportunityId,
                $project['agent_id'],
            ]
        );

        return [
            'registration_id' => $registrationId,
            'agent_id' => $project['agent_id'],
        ];
    }

    private function createCurrentTransactionRegistrationFixture($accountability = false)
    {
        $connection = $this->app->em->getConnection();
        $project = $connection->fetchAssoc(
            'SELECT id AS project_id, agent_id
             FROM project
             WHERE agent_id IS NOT NULL
             ORDER BY id
             LIMIT 1'
        );

        if (!$project) {
            throw new \RuntimeException('The test database must contain a project with an owner');
        }

        $opportunityId = $connection->fetchColumn(
            "INSERT INTO opportunity
                (agent_id, type, name, short_description, published_registrations,
                 create_timestamp, status, object_type, object_id)
             VALUES
                (?, 1, 'Evaluation regression test', 'Regression test', FALSE,
                 NOW(), 1, 'MapasCulturais\\Entities\\Project', ?)
             RETURNING id",
            [$project['agent_id'], $project['project_id']]
        );

        $configurationId = $connection->fetchColumn(
            'INSERT INTO evaluation_method_configuration (opportunity_id, type)
             VALUES (?, ?)
             RETURNING id',
            [$opportunityId, $accountability ? 'accountability' : 'simple']
        );

        if ($accountability) {
            $connection->executeUpdate(
                "INSERT INTO opportunity_meta (object_id, key, value)
                 VALUES (?, 'isAccountabilityPhase', '1')",
                [$opportunityId]
            );
        }

        $registrationId = $connection->fetchColumn(
            "INSERT INTO registration
                (opportunity_id, agent_id, create_timestamp, status, agents_data)
             VALUES
                (?, ?, NOW(), 0, '{}')
             RETURNING id",
            [$opportunityId, $project['agent_id']]
        );

        return [
            'registration_id' => (int) $registrationId,
            'opportunity_id' => (int) $opportunityId,
            'configuration_id' => (int) $configurationId,
            'agent_id' => (int) $project['agent_id'],
        ];
    }

    private function requireSubprocessSupport()
    {
        if (!function_exists('proc_open')) {
            $this->markTestSkipped('proc_open is required for the concurrent request regression test');
        }
    }

    private function createIndependentConnection()
    {
        $parameters = $this->app->em->getConnection()->getParams();
        $host = $parameters['host'] ?? 'localhost';
        $port = $parameters['port'] ?? 5432;
        $database = $parameters['dbname'];
        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";

        return new \PDO(
            $dsn,
            $parameters['user'],
            $parameters['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    private function createCommittedRegistrationFixture(\PDO $database)
    {
        $database->beginTransaction();

        try {
            $project = $database->query(
                'SELECT id AS project_id, agent_id
                 FROM project
                 WHERE agent_id IS NOT NULL
                 ORDER BY id
                 LIMIT 1'
            )->fetch();
            $userId = $database->query('SELECT id FROM usr ORDER BY id LIMIT 1')->fetchColumn();

            if (!$project || !$userId) {
                throw new \RuntimeException('The test database needs a project owner and a user');
            }

            $statement = $database->prepare(
                "INSERT INTO opportunity
                    (agent_id, type, name, short_description, published_registrations,
                     create_timestamp, status, object_type, object_id)
                 VALUES
                    (?, 1, 'Concurrent submission regression test', 'Regression test', FALSE,
                     NOW(), 1, 'MapasCulturais\\Entities\\Project', ?)
                 RETURNING id"
            );
            $statement->execute([
                $project['agent_id'],
                $project['project_id'],
            ]);
            $opportunityId = $statement->fetchColumn();

            $statement = $database->prepare(
                "INSERT INTO evaluation_method_configuration (opportunity_id, type)
                 VALUES (?, 'simple')
                 RETURNING id"
            );
            $statement->execute([$opportunityId]);
            $configurationId = $statement->fetchColumn();

            $statement = $database->prepare(
                "INSERT INTO registration
                    (opportunity_id, agent_id, create_timestamp, status, agents_data)
                 VALUES
                    (?, ?, NOW(), 0, '{}')
                 RETURNING id"
            );
            $statement->execute([
                $opportunityId,
                $project['agent_id'],
            ]);
            $registrationId = $statement->fetchColumn();

            $database->commit();

            return [
                'registration_id' => (int) $registrationId,
                'opportunity_id' => (int) $opportunityId,
                'configuration_id' => (int) $configurationId,
                'agent_id' => (int) $project['agent_id'],
                'user_id' => (int) $userId,
            ];
        } catch (\Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }
            throw $exception;
        }
    }

    private function createCommittedEvaluation(\PDO $database, array $fixture)
    {
        $statement = $database->prepare(
            "INSERT INTO registration_evaluation
                (id, registration_id, user_id, evaluation_data, status, create_timestamp)
             VALUES
                (nextval('registration_evaluation_id_seq'), ?, ?, ?, 0, NOW())
             RETURNING id"
        );
        $statement->execute([
            $fixture['registration_id'],
            $fixture['user_id'],
            json_encode(['status' => 10]),
        ]);

        return (int) $statement->fetchColumn();
    }

    private function createEvaluationWorkerCode($registrationId, $userId, $tag)
    {
        return sprintf(
            <<<'PHP'
try {
    require %s;
    // The legacy test bootstrap does not autoload module entity subclasses.
    require_once %s;
    $app = \MapasCulturais\App::i();
    $connection = $app->em->getConnection();
    $connection->executeQuery('SET application_name TO ' . $connection->quote(%s));
    $connection->executeQuery("SET lock_timeout TO '10s'");
    $app->disableAccessControl();
    $registration = $app->repo('Registration')->find(%d);
    $user = $app->repo('User')->find(%d);
    $evaluation = $registration->initializeUserEvaluation($user);
    if (!$evaluation || !$evaluation->id) {
        throw new \RuntimeException('The evaluation was not initialized');
    }
    fwrite(STDOUT, (string) $evaluation->id);
} catch (\Throwable $exception) {
    fwrite(STDERR, get_class($exception) . ': ' . $exception->getMessage());
    exit(1);
}
PHP
            ,
            var_export(__DIR__ . '/bootstrap.php', true),
            var_export(__DIR__ . '/../src/protected/application/lib/modules/Diligence/Entities/DiligenceFile.php', true),
            var_export($tag, true),
            $registrationId,
            $userId
        );
    }

    private function createOpinionWorkerCode($registrationId, $userId, $tag)
    {
        return sprintf(
            <<<'PHP'
try {
    require %s;
    // The legacy test bootstrap does not autoload these Diligence module symbols.
    require_once %s;
    require_once %s;
    $app = \MapasCulturais\App::i();
    $connection = $app->em->getConnection();
    $connection->executeQuery('SET application_name TO ' . $connection->quote(%s));
    $connection->executeQuery("SET lock_timeout TO '10s'");
    $user = $app->repo('User')->find(%d);
    $app->auth->setAuthenticatedUser($user);
    $app->disableAccessControl();
    $controller = $app->controller('diligence');
    if (!$controller) {
        throw new \RuntimeException('The diligence controller is not registered');
    }
    $method = new \ReflectionMethod($controller, 'createOrUpdateOpinion');
    $method->setAccessible(true);
    $method->invoke($controller, [
        'registrationId' => %d,
        'opinion' => 'Concurrent published opinion',
    ], true);
    $opinion = $app->repo(\Diligence\Entities\Opinion::class)->findOneBy([
        'registration' => %d,
    ]);
    if (!$opinion || !$opinion->id) {
        throw new \RuntimeException('The opinion was not created');
    }
    fwrite(STDOUT, (string) $opinion->id);
} catch (\Throwable $exception) {
    fwrite(STDERR, get_class($exception) . ': ' . $exception->getMessage());
    exit(1);
}
PHP
            ,
            var_export(__DIR__ . '/bootstrap.php', true),
            var_export(__DIR__ . '/../src/protected/application/lib/modules/Diligence/Entities/DiligenceFile.php', true),
            var_export(__DIR__ . '/../src/protected/application/lib/modules/Diligence/Traits/DiligenceSingle.php', true),
            var_export($tag, true),
            $userId,
            $registrationId,
            $registrationId
        );
    }

    private function createLegacyEvaluationSaveWorkerCode($evaluationId, $tag)
    {
        return sprintf(
            <<<'PHP'
try {
    require %s;
    require_once %s;
    $app = \MapasCulturais\App::i();
    $connection = $app->em->getConnection();
    $connection->executeQuery('SET application_name TO ' . $connection->quote(%s));
    $connection->executeQuery("SET lock_timeout TO '10s'");
    $app->disableAccessControl();
    $evaluation = $app->repo('RegistrationEvaluation')->find(%d);
    if (!$evaluation) {
        throw new \RuntimeException('The legacy evaluation does not exist');
    }
    $evaluation->evaluationData = ['status' => 3];
    $evaluation->status = \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED;
    $evaluation->save(true);
    fwrite(STDOUT, (string) $evaluation->id);
} catch (\Throwable $exception) {
    fwrite(STDERR, get_class($exception) . ': ' . $exception->getMessage());
    exit(1);
}
PHP
            ,
            var_export(__DIR__ . '/bootstrap.php', true),
            var_export(__DIR__ . '/../src/protected/application/lib/modules/Diligence/Entities/DiligenceFile.php', true),
            var_export($tag, true),
            $evaluationId
        );
    }

    private function createEvaluationDeleteWorkerCode($evaluationId, $tag)
    {
        return sprintf(
            <<<'PHP'
try {
    require %s;
    require_once %s;
    $app = \MapasCulturais\App::i();
    $connection = $app->em->getConnection();
    $connection->executeQuery('SET application_name TO ' . $connection->quote(%s));
    $connection->executeQuery("SET lock_timeout TO '10s'");
    $app->disableAccessControl();
    $evaluation = $app->repo('RegistrationEvaluation')->find(%d);
    if (!$evaluation) {
        throw new \RuntimeException('The evaluation to delete does not exist');
    }
    $deletedId = $evaluation->id;
    $evaluation->delete(true);
    fwrite(STDOUT, (string) $deletedId);
} catch (\Throwable $exception) {
    fwrite(STDERR, get_class($exception) . ': ' . $exception->getMessage());
    exit(1);
}
PHP
            ,
            var_export(__DIR__ . '/bootstrap.php', true),
            var_export(__DIR__ . '/../src/protected/application/lib/modules/Diligence/Entities/DiligenceFile.php', true),
            var_export($tag, true),
            $evaluationId
        );
    }

    private function createSaveUserEvaluationWorkerCode($registrationId, $userId, $tag)
    {
        return sprintf(
            <<<'PHP'
try {
    require %s;
    require_once %s;
    $app = \MapasCulturais\App::i();
    $connection = $app->em->getConnection();
    $connection->executeQuery('SET application_name TO ' . $connection->quote(%s));
    $connection->executeQuery("SET lock_timeout TO '10s'");
    $app->disableAccessControl();
    $registration = $app->repo('Registration')->find(%d);
    $user = $app->repo('User')->find(%d);
    $evaluation = $registration->saveUserEvaluation(
        ['status' => 8],
        $user,
        \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED
    );
    if (!$evaluation || !$evaluation->id) {
        throw new \RuntimeException('saveUserEvaluation did not return an evaluation');
    }
    fwrite(STDOUT, (string) $evaluation->id);
} catch (\Throwable $exception) {
    fwrite(STDERR, get_class($exception) . ': ' . $exception->getMessage());
    exit(1);
}
PHP
            ,
            var_export(__DIR__ . '/bootstrap.php', true),
            var_export(__DIR__ . '/../src/protected/application/lib/modules/Diligence/Entities/DiligenceFile.php', true),
            var_export($tag, true),
            $registrationId,
            $userId
        );
    }

    private function startWorker($code)
    {
        $stdout = tempnam(sys_get_temp_dir(), 'submission-race-out-');
        $stderr = tempnam(sys_get_temp_dir(), 'submission-race-err-');
        $command = escapeshellarg(PHP_BINARY)
            . ' -d display_errors=1 -d log_errors=0 -d error_reporting=1 -r '
            . escapeshellarg($code);
        $process = proc_open(
            $command,
            [
                0 => ['file', '/dev/null', 'r'],
                1 => ['file', $stdout, 'w'],
                2 => ['file', $stderr, 'w'],
            ],
            $pipes
        );

        if (!is_resource($process)) {
            @unlink($stdout);
            @unlink($stderr);
            throw new \RuntimeException('Unable to start concurrent test worker');
        }

        return [
            'process' => $process,
            'stdout' => $stdout,
            'stderr' => $stderr,
            'finished' => false,
        ];
    }

    private function waitForWorkersToBlock(\PDO $database, $tag, $expectedWorkers, array $workers)
    {
        $statement = $database->prepare(
            "SELECT COUNT(*)
             FROM pg_stat_activity
             WHERE application_name = ?
               AND wait_event_type = 'Lock'
               AND query LIKE '%SELECT id FROM registration WHERE id = %FOR UPDATE%'"
        );
        // Cold application boot can be slow in CI; lock_timeout still bounds
        // each worker once it reaches the critical section.
        $deadline = microtime(true) + 45;

        do {
            $statement->execute([$tag]);
            if ((int) $statement->fetchColumn() === $expectedWorkers) {
                return;
            }
            usleep(100000);
        } while (microtime(true) < $deadline);

        $activityStatement = $database->prepare(
            "SELECT pid, state, wait_event_type, wait_event, query
             FROM pg_stat_activity
             WHERE application_name = ?"
        );
        $activityStatement->execute([$tag]);
        $activity = $activityStatement->fetchAll();
        $workerDiagnostics = [];

        foreach ($workers as $worker) {
            $status = proc_get_status($worker['process']);
            $workerDiagnostics[] = [
                'running' => $status['running'],
                'exitcode' => $status['exitcode'],
                'stdout' => file_get_contents($worker['stdout']),
                'stderr' => file_get_contents($worker['stderr']),
            ];
        }

        throw new \RuntimeException(sprintf(
            '%d workers did not reach the registration lock. activity=%s workers=%s',
            $expectedWorkers,
            json_encode($activity),
            json_encode($workerDiagnostics)
        ));
    }

    private function waitForTaggedWorkersToWait(\PDO $database, array $tags, array $workers)
    {
        $placeholders = implode(', ', array_fill(0, count($tags), '?'));
        $statement = $database->prepare(
            "SELECT application_name, COUNT(*)
             FROM pg_stat_activity
             WHERE application_name IN ({$placeholders})
               AND wait_event_type = 'Lock'
             GROUP BY application_name"
        );
        $deadline = microtime(true) + 45;

        do {
            $statement->execute($tags);
            $waitingTags = $statement->fetchAll(\PDO::FETCH_KEY_PAIR);
            $allWaiting = true;

            foreach ($tags as $tag) {
                if (empty($waitingTags[$tag])) {
                    $allWaiting = false;
                    break;
                }
            }

            if ($allWaiting) {
                return;
            }

            usleep(100000);
        } while (microtime(true) < $deadline);

        $activityStatement = $database->prepare(
            "SELECT application_name, pid, state, wait_event_type, wait_event, query
             FROM pg_stat_activity
             WHERE application_name IN ({$placeholders})"
        );
        $activityStatement->execute($tags);
        $workerDiagnostics = [];

        foreach ($workers as $worker) {
            $status = proc_get_status($worker['process']);
            $workerDiagnostics[] = [
                'running' => $status['running'],
                'exitcode' => $status['exitcode'],
                'stdout' => file_get_contents($worker['stdout']),
                'stderr' => file_get_contents($worker['stderr']),
            ];
        }

        throw new \RuntimeException(sprintf(
            'Workers did not reach the expected locks. activity=%s workers=%s',
            json_encode($activityStatement->fetchAll()),
            json_encode($workerDiagnostics)
        ));
    }

    private function assertWorkerSucceeded(array &$worker)
    {
        $deadline = microtime(true) + 15;
        $status = proc_get_status($worker['process']);

        while ($status['running'] && microtime(true) < $deadline) {
            usleep(100000);
            $status = proc_get_status($worker['process']);
        }

        if ($status['running']) {
            proc_terminate($worker['process']);
            $this->fail('A concurrent submission worker timed out');
        }

        $closeStatus = proc_close($worker['process']);
        $worker['finished'] = true;
        $exitCode = $closeStatus >= 0 ? $closeStatus : $status['exitcode'];
        $stdout = file_get_contents($worker['stdout']);
        $stderr = file_get_contents($worker['stderr']);
        @unlink($worker['stdout']);
        @unlink($worker['stderr']);

        $this->assertSame(
            0,
            $exitCode,
            "Concurrent worker failed. stdout: {$stdout}; stderr: {$stderr}"
        );
        $this->assertNotSame('', trim($stdout), 'Concurrent worker must return the persisted entity id');
    }

    private function terminateWorkers(array &$workers)
    {
        foreach ($workers as &$worker) {
            if (!empty($worker['finished'])) {
                continue;
            }

            if (isset($worker['process']) && is_resource($worker['process'])) {
                $status = proc_get_status($worker['process']);
                if ($status['running']) {
                    proc_terminate($worker['process']);
                }
                proc_close($worker['process']);
            }

            if (isset($worker['stdout'])) {
                @unlink($worker['stdout']);
            }
            if (isset($worker['stderr'])) {
                @unlink($worker['stderr']);
            }
        }
        unset($worker);
    }

    private function deleteCommittedRegistrationFixture(\PDO $database, array $fixture)
    {
        $database->beginTransaction();

        try {
            $removedEvaluationIds = array_values(array_filter(array_map(
                'intval',
                $fixture['removed_evaluation_ids'] ?? []
            )));

            if ($removedEvaluationIds) {
                $placeholders = implode(', ', array_fill(0, count($removedEvaluationIds), '?'));
                $statement = $database->prepare(
                    "DELETE FROM entity_revision
                     WHERE object_type::varchar = 'MapasCulturais\\Entities\\RegistrationEvaluation'
                       AND object_id IN ({$placeholders})"
                );
                $statement->execute($removedEvaluationIds);
            }

            $statement = $database->prepare(
                "DELETE FROM entity_revision
                 WHERE (object_type::varchar = 'MapasCulturais\\Entities\\RegistrationEvaluation'
                        AND object_id IN (
                            SELECT id FROM registration_evaluation WHERE registration_id = ?
                        ))
                    OR (object_type::varchar = 'Diligence\\Entities\\Opinion'
                        AND object_id IN (
                            SELECT id FROM accountability_opinion WHERE registration_id = ?
                        ))
                    OR (object_type::varchar = 'MapasCulturais\\Entities\\Registration'
                        AND object_id = ?)"
            );
            $statement->execute([
                $fixture['registration_id'],
                $fixture['registration_id'],
                $fixture['registration_id'],
            ]);

            $statement = $database->prepare('DELETE FROM accountability_opinion WHERE registration_id = ?');
            $statement->execute([$fixture['registration_id']]);
            $statement = $database->prepare('DELETE FROM registration_evaluation WHERE registration_id = ?');
            $statement->execute([$fixture['registration_id']]);
            $statement = $database->prepare('DELETE FROM registration WHERE id = ?');
            $statement->execute([$fixture['registration_id']]);
            $statement = $database->prepare('DELETE FROM evaluation_method_configuration WHERE id = ?');
            $statement->execute([$fixture['configuration_id']]);
            $statement = $database->prepare('DELETE FROM opportunity WHERE id = ?');
            $statement->execute([$fixture['opportunity_id']]);

            $database->commit();
        } catch (\Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }
            throw $exception;
        }
    }
}
