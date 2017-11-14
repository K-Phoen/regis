<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Spec\Repository;

use RulerZ\Spec\ComposedSpecification;

use Regis\AppContext\Domain\Entity;

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
