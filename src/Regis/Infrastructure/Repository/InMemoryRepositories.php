<?php

namespace Regis\Infrastructure\Repository;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

class InMemoryRepositories implements Repository\Repositories
{
    private $repositories = [];

    public function __construct(array $repositories)
    {
        foreach ($repositories as $identifier => $repository) {
            $this->repositories[$identifier] = new Entity\Github\Repository($identifier, $repository['secret']);
        }
    }

    public function save(Entity\Repository $repository)
    {
        $this->repositories[$repository->getIdentifier()] = $repository;
    }

    public function findAll(): \Traversable
    {
        foreach ($this->repositories as $repo) {
            yield $repo;
        }
    }

    public function find(string $id): Entity\Repository
    {
        if (!array_key_exists($id, $this->repositories)) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }
        
        return $this->repositories[$id];
    }
}
