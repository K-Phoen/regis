<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Spec\Repository;

use RulerZ\Spec\AbstractSpecification;
use Regis\AppContext\Domain\Entity;

class AccessibleThroughTeam extends AbstractSpecification
{
    private $user;

    public function __construct(Entity\User $user)
    {
        $this->user = $user;
    }

    public function getRule()
    {
        // TODO I took a shortcut here.
        return 'teams.id IN :teams_ids';
    }

    public function getParameters()
    {
        $teamsIds = array_map(function (Entity\Team $team) {
            return $team->getId();
        }, iterator_to_array($this->user->getTeams()));

        return [
            'teams_ids' => $teamsIds,
        ];
    }
}
