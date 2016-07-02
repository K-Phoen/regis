<?php

namespace Tests\Regis\Application\CommandHandler\Github\Inspection;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Repository;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class SchedulePullRequestTest extends \PHPUnit_Framework_TestCase
{
    private $producer;
    private $repositoriesRepo;
    private $inspectionsRepo;
    /** @var CommandHandler\Github\Inspection\SchedulePullRequest */
    private $handler;

    public function setUp()
    {
        $this->producer = $this->getMockBuilder(ProducerInterface::class)->getMock();
        $this->repositoriesRepo = $this->getMockBuilder(Repository\Repositories::class)->getMock();
        $this->inspectionsRepo = $this->getMockBuilder(Repository\Inspections::class)->getMock();

        $this->handler = new CommandHandler\Github\Inspection\SchedulePullRequest($this->producer, $this->repositoriesRepo, $this->inspectionsRepo);
    }

    public function testItSavesTheInspectionAndSchedulesIt()
    {
        $pullRequest = $this->getMockBuilder(Model\Github\PullRequest::class)->disableOriginalConstructor()->getMock();
        $repository = $this->getMockBuilder(Entity\Github\Repository::class)->disableOriginalConstructor()->getMock();
        $command = new Command\Github\Inspection\SchedulePullRequest($pullRequest);

        $pullRequest->expects($this->once())
            ->method('getRepositoryIdentifier')
            ->will($this->returnValue('repo identifier'));
        $pullRequest->expects($this->once())
            ->method('toArray');

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('repo identifier')
            ->will($this->returnValue($repository));

        $this->inspectionsRepo->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Entity\Github\PullRequestInspection::class));

        $this->producer->expects($this->once())
            ->method('publish')
            ->with($this->anything());

        $this->handler->handle($command);
    }
}
