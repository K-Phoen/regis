<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\DeployKey;

class Add
{
    private $owner;
    private $repo;
    private $keyContent;

    public function __construct(string $owner, string $repo, string $keyContent)
    {
        $this->owner = $owner;
        $this->repo = $repo;
        $this->keyContent = $keyContent;
    }

    public function getRepositoryIdentifier(): string
    {
        return $this->owner.'/'.$this->repo;
    }

    public function getKeyContent(): string
    {
        return $this->keyContent;
    }
}
