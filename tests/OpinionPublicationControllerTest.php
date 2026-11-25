<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use MapasCulturais\App;
use MapasCulturais\Entities\ProjectOpportunity;
use MapasCulturais\Entities\Registration;
use OpinionManagement\Controllers\Controller;
use PHPUnit\Framework\TestCase;

require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Services/OpinionPublication.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Services/EvaluationReviewers.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Module.php';
require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Controllers/Controller.php';

class OpinionPublicationControllerTest extends TestCase
{
    private $instances;
    private $previousInstances;
    private $previousTimeLimit;
    private $connection;
    private $em;
    private $opportunity;
    private $opportunityRepository;
    private $registrationRepository;
    private $logger;
    private $canManage;

    protected function setUp(): void
    {
        $this->previousTimeLimit = (int) ini_get('max_execution_time');
        $this->instances = new ReflectionProperty(App::class, '_singletonInstances');
        $this->instances->setAccessible(true);
        $this->previousInstances = $this->instances->getValue();
        $this->connection = $this->createMock(Connection::class);
        $this->em = $this->createMock(EntityManager::class);
        $this->em->method('getConnection')->willReturn($this->connection);
        $this->logger = new class {
            public $errors = [];
            public function error($message) { $this->errors[] = $message; }
            public function debug($message) {}
        };
        $this->canManage = true;
        $this->opportunity = $this->getMockBuilder(ProjectOpportunity::class)->disableOriginalConstructor()
            ->setMethods(['__get', 'canUser'])->getMock();
        $configuration = $this->getMockBuilder(\MapasCulturais\Entities\EvaluationMethodConfiguration::class)
            ->disableOriginalConstructor()->setMethods(['__get', 'getAgentRelations'])->getMock();
        $configuration->method('__get')->willReturnMap([['type', (object) ['id' => 'technical']]]);
        $configuration->method('getAgentRelations')->willReturn([]);
        $this->opportunity->method('__get')->willReturnMap([
            ['id', 10], ['autopublishOpinions', 'Sim'], ['publishedOpinions', true],
            ['evaluationMethodConfiguration', $configuration],
        ]);
        $this->opportunity->method('canUser')->willReturnCallback(function () { return $this->canManage; });
        $this->opportunityRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $this->opportunityRepository->method('find')->willReturn($this->opportunity);
        $this->registrationRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $app = $this->getMockBuilder(App::class)->disableOriginalConstructor()->setMethods(['__get', 'repo'])->getMock();
        $app->method('__get')->willReturnMap([
            ['em', $this->em], ['user', (object) ['id' => 42]], ['log', $this->logger],
        ]);
        $app->method('repo')->willReturnCallback(function ($class) {
            return in_array($class, ['Opportunity', \MapasCulturais\Entities\Opportunity::class], true)
                ? $this->opportunityRepository : $this->registrationRepository;
        });
        $this->instances->setValue(null, [App::class => $app]);
    }

    protected function tearDown(): void
    {
        set_time_limit($this->previousTimeLimit);
        $this->instances->setValue(null, $this->previousInstances);
    }

    private function controller()
    {
        $controller = $this->getMockBuilder(Controller::class)->disableOriginalConstructor()
            ->setMethods(['getPostData', 'requireAuthentication', 'json'])->getMock();
        $controller->method('getPostData')->willReturn(['id' => 10, 'registrationIds' => [1]]);
        $controller->expects($this->once())->method('requireAuthentication');
        return $controller;
    }

    private function registration(int $id)
    {
        $registration = $this->getMockBuilder(Registration::class)->disableOriginalConstructor()
            ->setMethods(['__get', 'getMetadata', 'getSingleUrl'])->getMock();
        $registration->method('__get')->willReturnMap([
            ['id', $id], ['status', 1], ['number', 'on-' . $id], ['opportunity', $this->opportunity],
            ['owner', (object) ['name' => 'Proponente', 'user' => (object) ['id' => 42]]],
        ]);
        $registration->expects($this->never())->method('getMetadata');
        return $registration;
    }

    public function testAutomaticModeReturnsConflictWithoutPublishing(): void
    {
        $controller = $this->controller();
        $controller->expects($this->once())->method('json')->with($this->callback(function ($response) {
            return strpos($response['error'], 'automaticamente') !== false;
        }), 409);
        $this->connection->expects($this->once())->method('rollBack');
        $this->connection->expects($this->never())->method('commit');
        $this->registrationRepository->expects($this->never())->method('findBy');
        $controller->POST_publishSelectedOpinions();
    }

    public function testAutomaticModeKeepsStateEndpointAvailable(): void
    {
        $this->registrationRepository->method('findBy')->willReturn([$this->registration(1)]);
        $this->connection->expects($this->never())->method('fetchAllKeyValue');
        $controller = $this->controller();
        $controller->expects($this->once())->method('json')->with($this->callback(function ($response) {
            return $response['states'][1]['published'] && !$response['states'][1]['ready'];
        }));
        $controller->POST_registrationStates();
    }

    public function testPublicationStillRequiresOpportunityControl(): void
    {
        $this->canManage = false;
        $controller = $this->controller();
        $controller->expects($this->once())->method('json')->with($this->arrayHasKey('error'), 403);
        $this->connection->expects($this->never())->method('beginTransaction');
        $controller->POST_publishSelectedOpinions();
    }

    public function testGlobalNotificationsExcludeIndividualPublicationsInOneQuery(): void
    {
        $this->registrationRepository->method('matching')->willReturn(new ArrayCollection([
            $this->registration(1), $this->registration(2),
        ]));
        $this->connection->expects($this->once())->method('fetchAllKeyValue')->with(
            $this->stringContains('FROM registration_meta'),
            ['key' => 'opinionPublication', 'ids' => [1, 2]],
            ['ids' => Connection::PARAM_INT_ARRAY]
        )->willReturn([1 => '{"publishedBy":42}', 2 => '{"publishedBy":42}']);
        $this->em->expects($this->never())->method('persist');
        $this->assertTrue(Controller::notificateUsers(10));
    }

    public function testSharedNotificationProcessingSetsExecutionBudget(): void
    {
        set_time_limit(1);
        $method = new ReflectionMethod(Controller::class, 'notifyRegistrations');
        $method->setAccessible(true);
        $this->assertTrue($method->invoke(null, new ArrayCollection(), [], 10));
        $this->assertSame(500, (int) ini_get('max_execution_time'));
    }

    public function testMailDispatchFailureLogsOpportunityIdAndException(): void
    {
        $registration = $this->registration(1);
        // Fail before creating the queue connection; no message is sent.
        $registration->method('getSingleUrl')->willThrowException(new RuntimeException('Simulated mail failure'));
        // Simulate unavailable internal notification storage without writing to a database.
        $this->em->method('getClassMetadata')->willThrowException(new RuntimeException('Simulated storage failure'));
        $method = new ReflectionMethod(Controller::class, 'notifyRegistrations');
        $method->setAccessible(true);
        $method->invoke(null, new ArrayCollection([$registration]), [2], 10);
        $this->assertStringContainsString('oportunidade 10 para a fila', $this->logger->errors[0]);
        $this->assertStringContainsString('Simulated mail failure', $this->logger->errors[0]);
    }
}
