<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Command\Repository;

use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

class AddDeployKey
{
    private $repo;
    private $keyContent;

    public function __construct(RepositoryIdentifier $repo, string $keyContent)
    {
        $this->repo = $repo;
        $this->keyContent = $keyContent;
    }

    public function getRepository(): RepositoryIdentifier
    {
        return $this->repo;
    }

    public function getKeyContent(): string
    {
        return $this->keyContent;
    }
}
