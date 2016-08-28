<?php

declare(strict_types=1);

namespace Regis\Application\Spec\Repository;

use RulerZ\Spec\ComposedSpecification;

use Regis\Domain\Entity;

class AccessibleBy extends ComposedSpecification
{
    private $user;

    public function __construct(Entity\User $user)
    {
        $this->user = $user;
    }

    protected function getSpecification()
    {
        return (new IsOwner($this->user))->orX(new AccessibleThroughTeam($this->user));
    }
}