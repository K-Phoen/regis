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

namespace Tests\Regis\BitbucketContext\Application\Reporter;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Reporter\Bitbucket as BitbucketReporter;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity\Repository;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\BitbucketContext\Domain\Model\ReviewComment;

class BitbucketTest extends TestCase
{
    /** @var BitbucketClientFactory */
    private $clientFactory;

    /** @var BitbucketReporter */
    private $reporter;

    public function setUp()
    {
        $this->clientFactory = $this->createMock(BitbucketClientFactory::class);

        $this->reporter = new BitbucketReporter($this->clientFactory);
    }

    public function testViolationsAreReportedAsReviewComments()
    {
        $repository = $this->createMock(Repository::class);
        $comment = $this->createMock(ReviewComment::class);
        $pullRequest = $this->createMock(PullRequest::class);
        $client = $this->createMock(BitbucketClient::class);

        $this->clientFactory->method('createForRepository')->with($repository)->willReturn($client);

        $client->expects($this->once())
            ->method('sendComment')
            ->with($pullRequest, $comment);

        $this->reporter->report($repository, $comment, $pullRequest);
    }
}
