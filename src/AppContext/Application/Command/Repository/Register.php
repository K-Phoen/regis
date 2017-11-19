<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command\Repository;

use Regis\Kernel;

class Register
{
    private $owner;
    private $identifier;
    private $type;
    private $name;
    private $sharedSecret;

    public function __construct(Kernel\User $owner, string $type, string $identifier, string $name, string $sharedSecret = null)
    {
        $this->owner = $owner;
        $this->identifier = $identifier;
        $this->type = $type;
        $this->name = $name;
        $this->sharedSecret = $sharedSecret;
    }

    public function getOwner(): Kernel\User
    {
        return $this->owner;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSharedSecret()
    {
        return $this->sharedSecret;
    }
}
