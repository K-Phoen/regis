<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Remote;

use Regis\AppContext\Application\Remote\Repositories;
use Regis\AppContext\Domain\Model;
use Regis\AppContext\Domain\Entity;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Repository as BitbucketRepository;
use Regis\BitbucketContext\Domain\Model as BitbucketModel;
use Regis\BitbucketContext\Domain\Repository\Exception as BitbucketException;
use Regis\Kernel;

class BitbucketRepositories implements Repositories
{
    private $clientFactory;
    private $usersRepo;

    public function __construct(BitbucketClientFactory $clientFactory, BitbucketRepository\Users $usersRepo)
    {
        $this->clientFactory = $clientFactory;
        $this->usersRepo = $usersRepo;
    }

    public function forUser(Kernel\User $user): \Traversable
    {
        try {
            $bitbucketUser = $this->usersRepo->findByAccountId($user->accountId());
        } catch (BitbucketException\NotFound $e) {
            return new \ArrayIterator([]);
        }

        $bitbucketClient = $this->clientFactory->createForUser($bitbucketUser);

        /** @var BitbucketModel\Repository $repository */
        foreach ($bitbucketClient->listRepositories() as $repository) {
            yield new Model\Repository(
                $repository->getIdentifier()->value(),
                $repository->getName(),
                $repository->getPublicUrl(),
                Entity\Repository::TYPE_BITBUCKET
            );
        }
    }
}
