<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Repository;

use Regis\Domain\Entity\User;

class Create
{
    private $owner;
    private $identifier;
    private $sharedSecret;

    public function __construct(User $owner, string $identifier, string $sharedSecret = null)
    {
        $this->owner = $owner;
        $this->identifier = $identifier;
        $this->sharedSecret = $sharedSecret;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSharedSecret()
    {
        return $this->sharedSecret;
    }
}
