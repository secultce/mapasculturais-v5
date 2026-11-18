<?php

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use MapasCulturais\App;
use MapasCulturais\Entities\EvaluationMethodConfiguration;
use MapasCulturais\Entities\EvaluationMethodConfigurationAgentRelation;
use MapasCulturais\Entities\ProjectOpportunity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationMeta;
use MapasCulturais\Entities\RegistrationEvaluation;
use MapasCulturais\Repositories\RegistrationEvaluation as EvaluationRepository;
use OpinionManagement\Module;
use OpinionManagement\Services\EvaluationReviewers;
use OpinionManagement\Services\OpinionPublication;
use PHPUnit\Framework\TestCase;

require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Services/OpinionPublication.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Services/EvaluationReviewers.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Module.php';

class OpinionPublicationTest extends TestCase
{
    private $instances;
    private $previousInstances;
    private $connection;
    private $em;
    private $repository;
    private $opportunity;
    private $relations;

    protected function setUp(): void
    {
        $this->instances = new ReflectionProperty(App::class, '_singletonInstances');
        $this->instances->setAccessible(true);
        $this->previousInstances = $this->instances->getValue();
        $app = $this->getMockBuilder(App::class)->disableOriginalConstructor()
            ->setMethods(['__get', 'repo'])->getMock();
        $this->connection = $this->createMock(Connection::class);
        $this->em = $this->createMock(EntityManager::class);
        $this->em->method('getConnection')->willReturn($this->connection);
        $this->repository = $this->getMockBuilder(EvaluationRepository::class)->disableOriginalConstructor()->getMock();
        $app->method('repo')->willReturn($this->repository);
        $app->method('__get')->willReturnMap([['em', $this->em], ['user', (object) ['id' => 42]]]);
        $this->instances->setValue(null, [App::class => $app]);
        $this->relations = [(object) [
            'group' => 'group-admin',
            'status' => EvaluationMethodConfigurationAgentRelation::STATUS_ENABLED,
            'agent' => (object) ['user' => (object) ['id' => 101]],
        ]];
        $configuration = $this->getMockBuilder(EvaluationMethodConfiguration::class)
            ->disableOriginalConstructor()->setMethods(['getAgentRelations'])->getMock();
        $configuration->method('getAgentRelations')->with(null, true)->willReturnCallback(function () {
            return $this->relations;
        });
        $this->opportunity = $this->getMockBuilder(ProjectOpportunity::class)->disableOriginalConstructor()->setMethods(['__get'])->getMock();
        $this->opportunity->method('__get')->willReturnMap([
            ['id', 10], ['publishedOpinions', false], ['evaluationMethodConfiguration', $configuration],
        ]);
    }

    protected function tearDown(): void
    {
        $this->instances->setValue(null, $this->previousInstances);
    }

    private function registration(int $id, bool $published = false, int $status = 1)
    {
        $registration = $this->getMockBuilder(Registration::class)->disableOriginalConstructor()
            ->setMethods(['__get', 'getMetadata', 'setMetadata'])->getMock();
        $registration->method('__get')->willReturnMap([
            ['id', $id], ['number', 'on-' . $id], ['status', $status], ['opportunity', $this->opportunity],
        ]);
        $metadata = $this->getMockBuilder(RegistrationMeta::class)->disableOriginalConstructor()->setMethods(['save'])->getMock();
        $registration->method('getMetadata')->willReturnCallback(function ($key, $object = false) use ($published, $metadata) {
            return $object ? $metadata : ($published ? '{"publishedBy":42}' : null);
        });
        return [$registration, $metadata];
    }

    public function testOnlyCompletedUnpublishedRegistrationsAreReady(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['registration_id' => 1, 'total' => 2, 'sent' => 2],
            ['registration_id' => 2, 'total' => 2, 'sent' => 1],
            ['registration_id' => 4, 'total' => 1, 'sent' => 1],
        ]);
        $registrations = [$this->registration(1)[0], $this->registration(2)[0], $this->registration(3)[0], $this->registration(4, true)[0]];
        $states = (new OpinionPublication())->states($this->opportunity, $registrations);
        $this->assertTrue($states[1]['ready']);
        $this->assertFalse(OpinionPublication::isPublished($registrations[1]));
        $this->assertFalse($states[2]['ready'], 'An assigned reviewer who has not sent blocks publication.');
        $this->assertFalse($states[3]['ready'], 'No evaluations cannot mean ready.');
        $this->assertFalse($states[4]['ready']);
        $this->assertTrue($states[4]['published']);
    }

    public function testDisabledReviewersAreExcludedFromPublicationCounts(): void
    {
        $this->relations[] = (object) [
            'group' => 'group-admin',
            'status' => EvaluationMethodConfigurationAgentRelation::STATUS_DISABLED,
            'agent' => (object) ['user' => (object) ['id' => 102]],
        ];
        $this->connection->expects($this->once())->method('fetchAllAssociative')->with(
            $this->callback(function ($sql) {
                return strpos($sql, 'valuer_user_id IN (:valuerUsers)') !== false;
            }),
            ['sent' => RegistrationEvaluation::STATUS_SENT,
                'opportunity' => 10, 'ids' => [1], 'valuerUsers' => [101]],
            ['ids' => Connection::PARAM_INT_ARRAY, 'valuerUsers' => Connection::PARAM_INT_ARRAY]
        )->willReturn([['registration_id' => 1, 'total' => 1, 'sent' => 1]]);

        $states = (new OpinionPublication())->states($this->opportunity, [$this->registration(1)[0]]);
        $this->assertTrue($states[1]['ready']);
    }

    public function testGlobalPublicationUsesOnlyActiveReviewers(): void
    {
        $this->relations[] = (object) [
            'group' => 'group-admin',
            'status' => EvaluationMethodConfigurationAgentRelation::STATUS_DISABLED,
            'agent' => (object) ['user' => (object) ['id' => 102]],
        ];
        $this->repository->expects($this->exactly(2))->method('countByOpportunityAndUsersAndStatus')
            ->withConsecutive(
                [$this->opportunity, [101]],
                [$this->opportunity, [101], [RegistrationEvaluation::STATUS_SENT]]
            )->willReturnOnConsecutiveCalls(1, 1);

        $this->assertTrue(Module::areAllEvaluationsSent($this->opportunity));
    }

    public function testActiveReviewersAreUniqueAndExcludeOtherGroupsAndMissingUsers(): void
    {
        $this->relations[] = clone $this->relations[0];
        $this->relations[] = (object) [
            'group' => 'other',
            'status' => EvaluationMethodConfigurationAgentRelation::STATUS_ENABLED,
            'agent' => (object) ['user' => (object) ['id' => 102]],
        ];
        $this->relations[] = (object) [
            'group' => 'group-admin',
            'status' => EvaluationMethodConfigurationAgentRelation::STATUS_ENABLED,
            'agent' => (object) ['user' => null],
        ];

        $this->assertSame([101], EvaluationReviewers::activeUserIds($this->opportunity));
    }

    public function testMissingConfigurationHasNoActiveReviewers(): void
    {
        $opportunity = $this->getMockBuilder(ProjectOpportunity::class)->disableOriginalConstructor()
            ->setMethods(['__get'])->getMock();
        $opportunity->method('__get')->with('evaluationMethodConfiguration')->willReturn(null);

        $this->assertSame([], EvaluationReviewers::activeUserIds($opportunity));
    }

    public function testNoActiveReviewersCannotMeanReady(): void
    {
        $this->relations[0]->status = EvaluationMethodConfigurationAgentRelation::STATUS_DISABLED;
        $this->connection->expects($this->never())->method('fetchAllAssociative');
        $this->repository->expects($this->never())->method('countByOpportunityAndUsersAndStatus');

        $states = (new OpinionPublication())->states($this->opportunity, [$this->registration(1)[0]]);
        $this->assertFalse($states[1]['ready']);
        $this->assertFalse(Module::areAllEvaluationsSent($this->opportunity));
    }

    public function testPendingSelectionRollsBackWithoutPublishingAnyRegistration(): void
    {
        [$ready, $metadata] = $this->registration(1);
        [$pending] = $this->registration(2);
        $ready->expects($this->never())->method('setMetadata');
        $metadata->expects($this->never())->method('save');
        $this->repository->method('findBy')->willReturn([$ready, $pending]);
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['registration_id' => 1, 'total' => 1, 'sent' => 1],
            ['registration_id' => 2, 'total' => 1, 'sent' => 0],
        ]);
        $this->connection->expects($this->once())->method('rollBack');
        $this->connection->expects($this->never())->method('commit');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('on-2');
        (new OpinionPublication())->publish($this->opportunity, [1, 2]);
    }

    public function testSelectionCannotContainRegistrationsFromAnotherOpportunity(): void
    {
        $this->repository->method('findBy')->with(['id' => [1, 2], 'opportunity' => $this->opportunity])
            ->willReturn([$this->registration(1)[0]]);
        $this->connection->expects($this->once())->method('rollBack');
        $this->em->expects($this->never())->method('flush');
        $this->expectException(InvalidArgumentException::class);
        (new OpinionPublication())->publish($this->opportunity, [1, 2]);
    }

    public function testPublicationPersistsAuditAndReturnsOnlyNewPublications(): void
    {
        [$new, $metadata] = $this->registration(1);
        [$alreadyPublished, $oldMetadata] = $this->registration(2, true);
        $new->expects($this->once())->method('setMetadata')->with('opinionPublication', $this->callback(function ($value) {
            $audit = json_decode($value, true);
            return $audit['publishedBy'] === 42 && isset($audit['publishedAt']);
        }));
        $metadata->expects($this->once())->method('save')->with(true);
        $oldMetadata->expects($this->never())->method('save');
        $this->repository->method('findBy')->willReturn([$new, $alreadyPublished]);
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['registration_id' => 1, 'total' => 1, 'sent' => 1],
        ]);
        $this->connection->expects($this->once())->method('commit');
        $this->connection->expects($this->never())->method('rollBack');
        $published = (new OpinionPublication())->publish($this->opportunity, [1, 2]);
        $this->assertSame([$new], $published->getValues());
    }

    public function testLegacyGlobalPublicationStillGrantsAccess(): void
    {
        $this->opportunity = $this->getMockBuilder(ProjectOpportunity::class)->disableOriginalConstructor()->setMethods(['__get'])->getMock();
        $this->opportunity->method('__get')->willReturnMap([['id', 10], ['publishedOpinions', true]]);
        $this->assertTrue(OpinionPublication::isPublished($this->registration(1)[0]));
    }
}
