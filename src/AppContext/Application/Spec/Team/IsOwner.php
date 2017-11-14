<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Spec\Team;

use RulerZ\Spec\AbstractSpecification;
use Regis\AppContext\Domain\Entity;

class IsOwner extends AbstractSpecification
{
    private $user;

    public function __construct(Entity\User $user)
    {
        $this->user = $user;
    }

    public function getRule()
    {
        return 'owner.id = :owner_user_id';
    }

    public function getParameters()
    {
        return ['owner_user_id' => $this->user->accountId()];
    }
}
