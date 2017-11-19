<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Application\Worker;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use PhpAmqpLib\Message\AMQPMessage;
use Regis\AnalysisContext\Application\Worker\InspectionRunner;
use Regis\AnalysisContext\Application\Command;

class InspectionRunnerTest extends TestCase
{
    const INSPECTION_ID = 'c72aa79a-283e-4f91-b2b0-6f98d49d3a91';
    const REVISIONS_HEAD = '9a18f878a4de4688d0938461bc05bf985c35a236';
    const REVISIONS_BASE = '4750665fa7efb4dbfadc0b23812f944a7e25fb66';
    const REPOSITORY_CLONE_URL = 'git@github.com:K-Phoen/regis-test.git';
    const REPOSITORY_IDENTIFIER = 'K-Phoen/regis-test';

    private $commandBus;
    private $worker;

    public function setUp()
    {
        $this->commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();

        $this->worker = new InspectionRunner($this->commandBus);
    }

    public function testItRunsTheInspectionViaTheCommandBus()
    {
        $message = $this->createMock(AMQPMessage::class);
        $message->method('getBody')->willReturn($this->message());

        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (Command\InspectRevisions $command) {
                $this->assertSame(self::INSPECTION_ID, $command->getInspectionId());
                $this->assertSame(self::REVISIONS_BASE, $command->getRevisions()->getBase());
                $this->assertSame(self::REVISIONS_HEAD, $command->getRevisions()->getHead());
                $this->assertSame(self::REPOSITORY_CLONE_URL, $command->getRepository()->getCloneUrl());
                $this->assertSame(self::REPOSITORY_IDENTIFIER, $command->getRepository()->getIdentifier());

                return true;
            }));

        $this->worker->execute($message);
    }

    private function message(): string
    {
        return json_encode([
            'inspection_id' => self::INSPECTION_ID,
            'repository' => [
                'clone_url' => self::REPOSITORY_CLONE_URL,
                'identifier' => self::REPOSITORY_IDENTIFIER,
            ],
            'revisions' => [
                'base' => self::REVISIONS_BASE,
                'head' => self::REVISIONS_HEAD,
            ],
        ]);
    }
}
