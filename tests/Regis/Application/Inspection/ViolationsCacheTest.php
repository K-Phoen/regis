<?php

namespace Tests\Regis\Application\Inspection;

use M6Web\Component\RedisMock\RedisMockFactory;
use Predis\ClientInterface as RedisClient;
use Regis\Application\Inspection\ViolationsCache;
use Regis\Application\Model;

class ViolationsCacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var RedisClient */
    private $redis;

    /** @var ViolationsCache */
    private $violationsCache;

    /** @var Model\Violation */
    private $violation;

    /** @var Model\Github\PullRequest */
    private $pullRequest;

    public function setUp()
    {
        $factory = new RedisMockFactory();
        $this->redis = $factory->getAdapter('Predis\Client', true);
        $this->violationsCache = new ViolationsCache($this->redis);

        $revisions = new Model\Git\Revisions('head sha', 'base sha');
        $repository = new Model\Github\Repository('K-Phoen', 'test', 'clone url');

        $this->violation = new Model\Violation(Model\Violation::ERROR, 'file.php', 4, 'Test violation');
        $this->pullRequest = new Model\Github\PullRequest($repository, 2, $revisions);
    }

    public function testWhenAViolationIsNotCached()
    {
        $this->assertFalse($this->violationsCache->has($this->violation, $this->pullRequest));
    }

    public function testWhenAViolationIsAlreadyCached()
    {
        $this->assertFalse($this->violationsCache->has($this->violation, $this->pullRequest));

        $this->violationsCache->save($this->violation, $this->pullRequest);

        $this->assertTrue($this->violationsCache->has($this->violation, $this->pullRequest));
    }

    public function testViolationsForAPullRequestCanBeCleared()
    {
        $this->violationsCache->save($this->violation, $this->pullRequest);

        $this->assertTrue($this->violationsCache->has($this->violation, $this->pullRequest));

        $this->violationsCache->clear($this->pullRequest);

        $this->assertFalse($this->violationsCache->has($this->violation, $this->pullRequest));
    }
}
