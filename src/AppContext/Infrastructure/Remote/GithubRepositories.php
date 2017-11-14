<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Remote;

use Regis\AppContext\Domain\Model;
use Regis\AppContext\Domain\Entity;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Model as GithubModel;
use Regis\GithubContext\Domain\Repository as GithubRepository;
use Regis\GithubContext\Domain\Repository\Exception as GithubException;

use Regis\Kernel;

class GithubRepositories implements Repositories
{
    private $clientFactory;
    private $usersRepo;

    public function __construct(GithubClientFactory $clientFactory, GithubRepository\Users $usersRepo)
    {
        $this->clientFactory = $clientFactory;
        $this->usersRepo = $usersRepo;
    }

    public function forUser(Kernel\User $user): \Traversable
    {
        try {
            $githubUser = $this->usersRepo->findByAccountId($user->accountId());
        } catch (GithubException\NotFound $e) {
            return new \ArrayIterator([]);
        }

        $githubClient = $this->clientFactory->createForUser($githubUser);

        /** @var GithubModel\Repository $repository */
        foreach ($githubClient->listRepositories() as $repository) {
            yield new Model\Repository(
                $repository->getIdentifier(),
                $repository->getIdentifier(),
                $repository->getPublicUrl(),
                Entity\Repository::TYPE_GITHUB
            );
        }
    }
}
