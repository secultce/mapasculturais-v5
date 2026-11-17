<?php

use Diligence\Entities\AnswerDiligence;
use Diligence\Entities\Diligence;
use MapasCulturais\App;
use PHPUnit\Framework\TestCase;

// These tests use real templates and entities without booting the application/database.
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/Diligence/Service/DiligenceInterface.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/Diligence/Traits/DiligenceSingle.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/Diligence/Entities/Diligence.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/Diligence/Entities/AnswerDiligence.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/Diligence/Entities/Tado.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/Diligence/Repositories/Diligence.php';

class DiligenceHistoryTest extends TestCase
{
    private $instances;
    private $previousInstances;

    protected function setUp(): void
    {
        $this->instances = new ReflectionProperty(App::class, '_singletonInstances');
        $this->instances->setAccessible(true);
        $this->previousInstances = $this->instances->getValue();
        $app = $this->getMockBuilder(App::class)->disableOriginalConstructor()
            ->setMethods(['repo', 'getEm', 'getConfig', 'getUser'])->getMock();
        $app->method('getConfig')->willReturn(['app.lcode' => 'pt_BR']);
        $repository = $this->getMockBuilder(Doctrine\ORM\EntityRepository::class)
            ->disableOriginalConstructor()->setMethods(['findOneBy', 'find'])->getMock();
        $repository->method('findOneBy')->willReturn(null);
        $repository->method('find')->willReturn((object) ['opportunity' => new class {
            public function canUser() { return true; }
        }]);
        $app->method('repo')->willReturn($repository);
        $connection = $this->createMock(Doctrine\DBAL\Connection::class);
        $connection->method('fetchAllAssociative')->willReturn([]);
        $em = $this->createMock(Doctrine\ORM\EntityManager::class);
        $em->method('getConnection')->willReturn($connection);
        $app->method('getEm')->willReturn($em);
        $this->instances->setValue(null, [App::class => $app]);
    }

    protected function tearDown(): void
    {
        $this->instances->setValue(null, $this->previousInstances);
    }

    private function entity($class, array $values)
    {
        $entity = (new ReflectionClass($class))->newInstanceWithoutConstructor();
        foreach ($values as $name => $value) {
            $property = new ReflectionProperty($class, $name);
            $property->setAccessible(true);
            $property->setValue($entity, $value);
        }
        return $entity;
    }

    private function pair($id, $status, $answerStatus = AnswerDiligence::STATUS_SEND)
    {
        $diligence = $this->entity(Diligence::class, [
            'id' => $id, 'status' => $status, 'description' => "message-$id",
            'sendDiligence' => new DateTime('2026-09-01'),
            'subject' => '["subject_exec_physical"]',
        ]);
        $answer = $this->entity(AnswerDiligence::class, [
            'status' => $answerStatus, 'answer' => "answer-$id", 'diligence' => $diligence,
            'createTimestamp' => new DateTime('2026-09-02'),
        ]);
        return [$diligence, $answer];
    }

    private function render($template, array $entries)
    {
        $renderer = new class {
            public function applyTemplateHook() {}
            public function part() {}
            public function render($path, $diligenceAndAnswers) {
                $entity = (object) ['id' => 123];
                ob_start();
                try {
                    include $path;
                    return ob_get_contents();
                } finally {
                    ob_end_clean();
                }
            }
        };
        $directory = dirname((new ReflectionClass(Diligence::class))->getFileName());
        return $renderer->render($directory . '/../layouts/parts/diligence/' . $template . '.php', $entries);
    }

    public function templates()
    {
        return array_map(function ($name) { return [$name]; }, [
            'body-diligence-common', 'body-diligence-multi',
            'body-proponent-common', 'body-proponent-multi',
        ]);
    }

    /** @dataProvider templates */
    public function testSentAnsweredAndCompletedRemainVisible($template)
    {
        foreach ([Diligence::STATUS_SEND, Diligence::STATUS_ANSWERED, Diligence::STATUS_COMPLETE] as $status) {
            $html = $this->render($template, $this->pair(1, $status));
            $this->assertStringContainsString('message-1', $html);
            $this->assertStringContainsString('answer-1', $html);
        }
    }

    public function testMultipleRoundsKeepMessagesAndAnswersInHistory()
    {
        foreach (['body-diligence-multi', 'body-proponent-multi'] as $template) {
            $entries = array_merge(
                $this->pair(3, Diligence::STATUS_SEND),
                $this->pair(2, Diligence::STATUS_ANSWERED),
                $this->pair(1, Diligence::STATUS_COMPLETE)
            );
            $html = $this->render($template, $entries);
            $this->assertStringContainsString('Mensagens mais antigas', $html);
            foreach ([1, 2, 3] as $id) {
                $this->assertStringContainsString("message-$id", $html);
                $this->assertStringContainsString("answer-$id", $html);
            }
        }
    }

    public function testProponentFilteringPreservesPairsAndHidesDrafts()
    {
        $entries = array_merge(
            $this->pair(3, Diligence::STATUS_DRAFT, AnswerDiligence::STATUS_DRAFT),
            [$this->pair(2, Diligence::STATUS_COMPLETE)[0], null],
            $this->pair(1, Diligence::STATUS_ANSWERED)
        );
        $html = $this->render('body-proponent-multi', $entries);
        $this->assertStringNotContainsString('message-3', $html);
        $this->assertStringNotContainsString('answer-3', $html);
        $this->assertStringContainsString('message-2', $html);
        $this->assertStringContainsString('message-1', $html);
        $this->assertStringContainsString('answer-1', $html);
    }

    /** @dataProvider templates */
    public function testDraftAnswersAreNotPublished($template)
    {
        $html = $this->render($template, $this->pair(1, Diligence::STATUS_COMPLETE, AnswerDiligence::STATUS_DRAFT));
        $this->assertStringContainsString('message-1', $html);
        $this->assertStringNotContainsString('answer-1', $html);
    }
    public function testCompletionOnlyUpdatesLatestDiligenceOfRegistration()
    {
        $config = Doctrine\ORM\Tools\Setup::createConfiguration(true);
        $config->setMetadataDriverImpl(new Doctrine\ORM\Mapping\Driver\StaticPHPDriver([]));
        $em = Doctrine\ORM\EntityManager::create(['driver' => 'pdo_sqlite', 'memory' => true], $config);

        // Minimal mappings keep this regression independent of the application's schema.
        $registration = new Doctrine\ORM\Mapping\ClassMetadata(MapasCulturais\Entities\Registration::class);
        $registration->setPrimaryTable(['name' => 'registration']);
        $registration->mapField(['fieldName' => 'id', 'type' => 'integer', 'id' => true]);
        $diligence = new Doctrine\ORM\Mapping\ClassMetadata(Diligence::class);
        $diligence->setPrimaryTable(['name' => 'diligence']);
        $diligence->mapField(['fieldName' => 'id', 'type' => 'integer', 'id' => true]);
        $diligence->mapField(['fieldName' => 'status', 'type' => 'integer']);
        $diligence->mapManyToOne([
            'fieldName' => 'registration', 'targetEntity' => MapasCulturais\Entities\Registration::class,
            'joinColumns' => [['name' => 'registration_id', 'referencedColumnName' => 'id']],
        ]);
        foreach ([$registration, $diligence] as $metadata) {
            $metadata->initializeReflection(new Doctrine\Persistence\Mapping\RuntimeReflectionService());
            $metadata->wakeupReflection(new Doctrine\Persistence\Mapping\RuntimeReflectionService());
            $em->getMetadataFactory()->setMetadataFor($metadata->name, $metadata);
        }
        (new Doctrine\ORM\Tools\SchemaTool($em))->createSchema([$registration, $diligence]);
        $connection = $em->getConnection();
        foreach ([123, 456, 789] as $id) {
            $connection->insert('registration', ['id' => $id]);
        }
        foreach ([[1, 123, 10], [2, 123, 4], [3, 123, 3], [4, 456, 3]] as $row) {
            $connection->insert('diligence', array_combine(['id', 'registration_id', 'status'], $row));
        }
        $app = $this->getMockBuilder(App::class)->disableOriginalConstructor()
            ->setMethods(['repo', 'getEm'])->getMock();
        $app->method('getEm')->willReturn($em);
        $app->method('repo')->willReturnCallback(function ($class) use ($em) {
            return $em->getRepository($class);
        });
        $this->instances->setValue(null, [App::class => $app]);

        \Diligence\Repositories\Diligence::updateLatestStatusByRegistration(123, Diligence::STATUS_COMPLETE);
        $this->assertEquals([10, 4, 10, 3], $connection->executeQuery('SELECT status FROM diligence ORDER BY id')->fetchFirstColumn());

        // The same query accepts the persisted registration, as passed by the TADO controller.
        $reg = $em->find(MapasCulturais\Entities\Registration::class, 456);
        \Diligence\Repositories\Diligence::updateLatestStatusByRegistration($reg, Diligence::STATUS_COMPLETE);
        $this->assertEquals([10, 4, 10, 10], $connection->executeQuery('SELECT status FROM diligence ORDER BY id')->fetchFirstColumn());

        // Registrations with no diligence must not affect any other registration.
        \Diligence\Repositories\Diligence::updateLatestStatusByRegistration(789, Diligence::STATUS_COMPLETE);
        $this->assertEquals([10, 4, 10, 10], $connection->executeQuery('SELECT status FROM diligence ORDER BY id')->fetchFirstColumn());
    }

}
