<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Spec\Team;

use RulerZ\Spec\ComposedSpecification;

use Regis\GithubContext\Domain\Entity;

class AccessibleBy extends ComposedSpecification
{
    private $user;

    public function __construct(Entity\User $user)
    {
        $this->user = $user;
    }

    protected function getSpecification()
    {
        return (new IsOwner($this->user))->orX(new IsMember($this->user));
    }
}
