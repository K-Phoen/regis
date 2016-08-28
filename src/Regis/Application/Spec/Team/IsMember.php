<?php

declare(strict_types=1);

namespace Regis\Application\Spec\Team;

use RulerZ\Spec\AbstractSpecification;

use Regis\Domain\Entity;

class IsMember extends AbstractSpecification
{
    private $user;

    public function __construct(Entity\User $user)
    {
        $this->user = $user;
    }

    public function getRule()
    {
        return 'members.id = :member_user_id';
    }

    public function getParameters()
    {
        return ['member_user_id' => $this->user->getId()];
    }
}