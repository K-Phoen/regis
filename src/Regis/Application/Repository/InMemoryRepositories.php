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

    public function find(string $identifier): Model\Repository
    {
        if (!array_key_exists($identifier, $this->repositories)) {
            throw Exception\NotFound::forIdentifier($identifier);
        }
        
        return new Model\Repository($identifier, $this->repositories[$identifier]['secret']);
    }
}
