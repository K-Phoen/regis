<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Command\Repository;

use Regis\BitbucketContext\Domain\Entity\User;

class Register
{
    private $owner;
    private $identifier;

    public function __construct(User $owner, string $identifier)
    {
        $this->owner = $owner;
        $this->identifier = $identifier;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
