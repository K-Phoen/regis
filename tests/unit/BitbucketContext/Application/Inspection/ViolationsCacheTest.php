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

namespace Tests\Regis\BitbucketContext\Application\Inspection;

use PHPUnit\Framework\TestCase;
use M6Web\Component\RedisMock\RedisMockFactory;
use Predis\ClientInterface as RedisClient;
use Regis\BitbucketContext\Application\Inspection\ViolationsCache;
use Regis\BitbucketContext\Domain\Model;

class ViolationsCacheTest extends TestCase
{
    /** @var RedisClient */
    private $redis;

    /** @var ViolationsCache */
    private $violationsCache;

    /** @var Model\ReviewComment */
    private $reviewComment;

    /** @var Model\PullRequest */
    private $pullRequest;

    public function setUp()
    {
        $factory = new RedisMockFactory();
        $this->redis = $factory->getAdapter(\Predis\Client::class, true);
        $this->violationsCache = new ViolationsCache($this->redis);

        $repositoryIdentifier = new Model\RepositoryIdentifier('repository-id');
        $this->reviewComment = new Model\ReviewComment('file.php', 42, 'Test violation');
        $this->pullRequest = new Model\PullRequest($repositoryIdentifier, 2, 'head-sha', 'base-sha');
    }

    public function testWhenAViolationIsNotCached()
    {
        $this->assertFalse($this->violationsCache->has($this->reviewComment, $this->pullRequest));
    }

    public function testWhenAViolationIsAlreadyCached()
    {
        $this->assertFalse($this->violationsCache->has($this->reviewComment, $this->pullRequest));

        $this->violationsCache->save($this->reviewComment, $this->pullRequest);

        $this->assertTrue($this->violationsCache->has($this->reviewComment, $this->pullRequest));
    }

    public function testViolationsForAPullRequestCanBeCleared()
    {
        $this->violationsCache->save($this->reviewComment, $this->pullRequest);

        $this->assertTrue($this->violationsCache->has($this->reviewComment, $this->pullRequest));

        $this->violationsCache->clear($this->pullRequest);

        $this->assertFalse($this->violationsCache->has($this->reviewComment, $this->pullRequest));
    }
}
