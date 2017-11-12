<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Repository;

use Regis\GithubContext\Domain\Entity;

class DefineSharedSecret
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
