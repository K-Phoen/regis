<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class InMemoryRepositories implements Repository\Repositories
{
    private $repositories = [];

    public function __construct(array $repositories)
    {
        /** @var Entity\Repository $repository */
        foreach ($repositories as $repository) {
            $this->repositories[$repository->getIdentifier()] = $repository;
        }
    }

    public function save(Entity\Repository $team)
    {
        $this->repositories[$team->getIdentifier()] = $team;
    }

    public function find(string $id): Entity\Repository
    {
        if (!array_key_exists($id, $this->repositories)) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $this->repositories[$id];
    }
}
