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

namespace Tests\Regis\GithubContext\Application\Reporter;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Reporter\Github as GithubReporter;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity\Repository;
use Regis\GithubContext\Domain\Entity\Violation;
use Regis\GithubContext\Domain\Model\PullRequest;

class GithubTest extends TestCase
{
    /** @var GithubClientFactory */
    private $clientFactory;

    /** @var GithubReporter */
    private $reporter;

    public function setUp()
    {
        $this->clientFactory = $this->createMock(GithubClientFactory::class);

        $this->reporter = new GithubReporter($this->clientFactory);
    }

    public function testViolationsAreReportedAsReviewCOmments()
    {
        $repository = $this->createMock(Repository::class);
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $client = $this->createMock(GithubClient::class);
        $client->expects($this->once())->method('sendComment');

        $this->clientFactory->expects($this->once())
            ->method('createForRepository')
            ->with($repository)
            ->willReturn($client);

        $this->reporter->report($repository, $violation, $pullRequest);
    }
}
