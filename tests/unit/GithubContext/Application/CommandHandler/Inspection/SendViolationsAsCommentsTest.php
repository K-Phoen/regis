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

namespace Tests\Regis\GithubContext\Application\CommandHandler\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Application\Reporter;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\GithubContext\Domain\Entity\Report;
use Regis\GithubContext\Domain\Entity\Violation;
use Regis\GithubContext\Domain\Entity\Repository;
use Regis\GithubContext\Domain\Model\PullRequest;

class SendViolationsAsCommentsTest extends TestCase
{
    private $reporter;
    /** @var CommandHandler\Inspection\SendViolationsAsComments */
    private $handler;

    public function setUp()
    {
        $this->reporter = $this->createMock(Reporter::class);

        $this->handler = new CommandHandler\Inspection\SendViolationsAsComments($this->reporter);
    }

    public function testViolationsAreTransferedToTheReporter()
    {
        $repository = $this->createMock(Repository::class);
        $pullRequest = $this->createMock(PullRequest::class);
        $inspection = $this->createMock(PullRequestInspection::class);
        $report = $this->createMock(Report::class);

        $violation1 = $this->createMock(Violation::class);
        $violation2 = $this->createMock(Violation::class);

        $inspection->method('getReport')->willReturn($report);
        $inspection->method('hasReport')->willReturn(true);
        $inspection->method('getRepository')->willReturn($repository);
        $inspection->method('getPullRequest')->willReturn($pullRequest);
        $report->method('violations')->willReturn(new \ArrayIterator([$violation1, $violation2]));

        $this->reporter->expects($this->exactly(2))
            ->method('report')
            ->withConsecutive(
                [$repository, $violation1, $pullRequest],
                [$repository, $violation1, $pullRequest]
            );

        $command = new Command\Inspection\SendViolationsAsComments($inspection);

        $this->handler->handle($command);
    }

    public function testAnInspectionWithNoReportTriggersNothing()
    {
        $inspection = $this->createMock(PullRequestInspection::class);

        $inspection->method('hasReport')->willReturn(false);

        $inspection->expects($this->never())->method('getReport');
        $this->reporter->expects($this->never())->method('report');

        $command = new Command\Inspection\SendViolationsAsComments($inspection);

        $this->handler->handle($command);
    }
}
