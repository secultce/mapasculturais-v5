<?php

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use MapasCulturais\App;
use MapasCulturais\Entities\ProjectOpportunity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationMeta;
use OpinionManagement\Services\OpinionPublication;
use PHPUnit\Framework\TestCase;

require_once dirname((new ReflectionClass(App::class))->getFileName()) . '/../modules/OpinionManagement/Services/OpinionPublication.php';

class OpinionPublicationTest extends TestCase
{
    private $instances;
    private $previousInstances;
    private $connection;
    private $em;
    private $repository;
    private $opportunity;

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
        $this->repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $app->method('repo')->willReturn($this->repository);
        $app->method('__get')->willReturnMap([['em', $this->em], ['user', (object) ['id' => 42]]]);
        $this->instances->setValue(null, [App::class => $app]);
        $this->opportunity = $this->getMockBuilder(ProjectOpportunity::class)->disableOriginalConstructor()->setMethods(['__get'])->getMock();
        $this->opportunity->method('__get')->willReturnMap([['id', 10], ['publishedOpinions', false]]);
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
