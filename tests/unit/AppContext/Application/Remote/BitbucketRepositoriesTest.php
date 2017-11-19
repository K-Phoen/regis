<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Infrastructure\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\User;
use Regis\AppContext\Application\Remote\BitbucketRepositories;
use Regis\AppContext\Application\Remote\Repositories;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Repository as BitbucketRepository;
use Regis\BitbucketContext\Domain\Model as BitbucketModel;
use Regis\BitbucketContext\Domain\Repository\Exception as BitbucketException;
use Regis\Kernel;
use Regis\AppContext\Domain\Model;
use Regis\AppContext\Domain\Entity;
use Tests\Regis\Helper\ObjectManipulationHelper;

class BitbucketRepositoriesTest extends TestCase
{
    use ObjectManipulationHelper;

    const ACCOUNT_ID = 'account-id';

    /** @var BitbucketClientFactory */
    private $ghClientFactory;
    /** @var BitbucketRepository\Users */
    private $usersRepo;
    /** @var Kernel\User */
    private $user;

    /** @var BitbucketRepositories */
    private $githubRepositories;

    public function setUp()
    {
        $this->ghClientFactory = $this->createMock(BitbucketClientFactory::class);
        $this->usersRepo = $this->createMock(BitbucketRepository\Users::class);
        $this->user = new User();

        $this->setPrivateValue($this->user, 'id', self::ACCOUNT_ID);

        $this->githubRepositories = new BitbucketRepositories($this->ghClientFactory, $this->usersRepo);
    }

    public function testItImplementsTheRightInterfaces()
    {
        $this->assertInstanceOf(Repositories::class, $this->githubRepositories);
    }

    public function testItReturnsAnEmptyListIfTheUserHasNoBitbucketProfile()
    {
        $this->usersRepo->method('findByAccountId')->with(self::ACCOUNT_ID)->willThrowException(new BitbucketException\NotFound());

        $repositories = $this->githubRepositories->forUser($this->user);

        $this->assertCount(0, $repositories);
    }

    public function testItFetchesTheRepositoriesFromBitbucketWhenTheUserHasABitbucketProfile()
    {
        $githubProfile = $this->createMock(BitbucketDetails::class);
        $githubClient = $this->createMock(BitbucketClient::class);
        $githubRepository = new BitbucketModel\Repository(
            new BitbucketModel\RepositoryIdentifier('some-identifier'),
            'repo-name',
            'clone-url',
            'public-url'
        );

        $this->usersRepo->method('findByAccountId')->with(self::ACCOUNT_ID)->willReturn($githubProfile);
        $this->ghClientFactory->method('createForUser')->with($githubProfile)->willReturn($githubClient);
        $githubClient->method('listRepositories')->willReturn(new \ArrayIterator([$githubRepository]));

        $repositories = iterator_to_array($this->githubRepositories->forUser($this->user));

        $this->assertCount(1, $repositories);

        /** @var Model\Repository $repository */
        $repository = current($repositories);
        $this->assertInstanceOf(Model\Repository::class, $repository);
        $this->assertSame('some-identifier', $repository->getIdentifier());
        $this->assertSame('repo-name', $repository->getName());
        $this->assertSame('public-url', $repository->getPublicUrl());
        $this->assertSame(Entity\Repository::TYPE_BITBUCKET, $repository->getType());
    }
}
