<?php

namespace Tests\Regis\Application\Worker;

use League\Tactician\CommandBus;
use PhpAmqpLib\Message\AMQPMessage;

use Regis\Application\Command;
use Regis\Application\Repository;
use Regis\Application\Worker\GithubPrInspectionRunner;
use Regis\Domain\Entity\Github\PullRequestInspection;

class GithubPrInspectionRunnerTest extends \PHPUnit_Framework_TestCase
{
    private $commandBus;
    private $inspectionsRepo;
    private $worker;

    public function setUp()
    {
        $this->commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $this->inspectionsRepo = $this->getMockBuilder(Repository\Inspections::class)->getMock();

        $this->worker = new GithubPrInspectionRunner($this->commandBus, $this->inspectionsRepo);
    }

    public function testItRunsTheInspectionViaTheCommandBus()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $message = $this->getMockBuilder(AMQPMessage::class)->getMock();
        $message->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($this->message()));

        $this->inspectionsRepo->expects($this->once())
            ->method('find')
            ->with('c72aa79a-283e-4f91-b2b0-6f98d49d3a91')
            ->will($this->returnValue($inspection));

        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Github\Inspection\InspectPullRequest::class));

        $this->worker->execute($message);
    }

    private function message(): string
    {
        return <<<MSG
{"inspection":"c72aa79a-283e-4f91-b2b0-6f98d49d3a91","pull_request":{"repository":{"clone_url":"git@github.com:K-Phoen\/regis-test.git","owner":"K-Phoen","name":"regis-test"},"number":5,"revisions":{"base":"4750665fa7efb4dbfadc0b23812f944a7e25fb66","head":"9a18f878a4de4688d0938461bc05bf985c35a236"}}}
MSG;
    }
}
