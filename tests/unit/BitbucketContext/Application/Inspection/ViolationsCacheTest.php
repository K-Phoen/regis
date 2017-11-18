<?php

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
