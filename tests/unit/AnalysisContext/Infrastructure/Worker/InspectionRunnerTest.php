<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Infrastructure\Worker;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\AnalysisContext\Infrastructure\Worker\InspectionRunner;
use Regis\AnalysisContext\Application\Command;
use Swarrot\Broker\Message;

class InspectionRunnerTest extends TestCase
{
    private const INSPECTION_ID = 'c72aa79a-283e-4f91-b2b0-6f98d49d3a91';
    private const REVISIONS_HEAD = '9a18f878a4de4688d0938461bc05bf985c35a236';
    private const REVISIONS_BASE = '4750665fa7efb4dbfadc0b23812f944a7e25fb66';
    private const REPOSITORY_CLONE_URL = 'git@github.com:K-Phoen/regis-test.git';
    private const REPOSITORY_IDENTIFIER = 'K-Phoen/regis-test';

    private $commandBus;
    private $worker;

    public function setUp()
    {
        $this->commandBus = $this->createMock(CommandBus::class);

        $this->worker = new InspectionRunner($this->commandBus);
    }

    public function testItRunsTheInspectionViaTheCommandBus()
    {
        $message = $this->createMock(Message::class);
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

        $this->worker->process($message, []);
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
