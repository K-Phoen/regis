<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Team;

use Regis\GithubContext\Domain\Entity;

class Create
{
    private $owner;
    private $name;

    public function __construct(Entity\User $owner, string $name)
    {
        $this->owner = $owner;
        $this->name = $name;
    }

    public function getOwner(): Entity\User
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
