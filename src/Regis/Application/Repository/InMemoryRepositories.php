<?php

namespace Regis\Application\Repository;

use Regis\Application\Model;

class InMemoryRepositories implements Repositories
{
    private $repositories = [];

    public function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    public function findAll(): \Traversable
    {
        foreach ($this->repositories as $identifier => $repo) {
            yield new Model\Repository($identifier, $repo['secret']);
        }
    }

    public function find(string $identifier): Model\Repository
    {
        if (!array_key_exists($identifier, $this->repositories)) {
            throw Exception\NotFound::forIdentifier($identifier);
        }
        
        return new Model\Repository($identifier, $this->repositories[$identifier]['secret']);
    }
}
