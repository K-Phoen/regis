<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Application\Inspection;

use PHPUnit\Framework\TestCase;
use M6Web\Component\RedisMock\RedisMockFactory;
use Predis\ClientInterface as RedisClient;
use Regis\GithubContext\Application\Inspection\ViolationsCache;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;
use Tests\Regis\Helper\ObjectManipulationHelper;

class ViolationsCacheTest extends TestCase
{
    use ObjectManipulationHelper;

    /** @var RedisClient */
    private $redis;

    /** @var ViolationsCache */
    private $violationsCache;

    /** @var Entity\Violation */
    private $violation;

    /** @var Model\PullRequest */
    private $pullRequest;

    public function setUp()
    {
        $factory = new RedisMockFactory();
        $this->redis = $factory->getAdapter(\Predis\Client::class, true);
        $this->violationsCache = new ViolationsCache($this->redis);

        $repositoryIdentifier = Model\RepositoryIdentifier::fromFullName('K-Phoen/test');

        $this->violation = new Entity\Violation();
        $this->setPrivateValue($this->violation, 'file', 'file.php');
        $this->setPrivateValue($this->violation, 'position', 4);
        $this->setPrivateValue($this->violation, 'description', 'Test violation');

        $this->pullRequest = new Model\PullRequest($repositoryIdentifier, 2, 'head-sha', 'base-sha');
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
