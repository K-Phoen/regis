<?php

declare(strict_types=1);

namespace Regis\Application\Command\Repository;

use Regis\Application\Entity;

class UpdateConfiguration
{
    private $repository;
    private $newSharedSecret;

    public function __construct(Entity\Repository $repository, string $newSharedSecret)
    {
        $this->repository = $repository;
        $this->newSharedSecret = $newSharedSecret;
    }

    public function getRepository(): Entity\Repository
    {
        return $this->repository;
    }

    public function getNewSharedSecret(): string
    {
        return $this->newSharedSecret;
    }
}