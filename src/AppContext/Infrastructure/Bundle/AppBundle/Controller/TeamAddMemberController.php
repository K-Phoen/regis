<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Regis\AppContext\Application\Spec;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;

class TeamAddMemberController extends Controller
{
    /**
     * TODO improve UI
     */
    public function addMemberAction(Entity\Team $team)
    {
        return $this->render('@RegisApp/Teams/add_member.html.twig', [
            'team' => $team,
        ]);
    }

    public function submitMembershipAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\AddMember($team, $request->request->get('new_member_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function removeMembershipAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\RemoveMember($team, $request->request->get('member_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function leaveAction(Entity\Team $team)
    {
        $command = new Command\Team\Leave($team, $this->getUser());

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function userSearchAction(Request $request)
    {
        $q = $request->query->get('q');

        // TODO should be in a command?
        if (empty($q)) {
            $results = [];
        } else {
            $results = $this->get('regis.app.repository.users')->matching(new Spec\User\Matches($q));
            // TODO eurk
            $results = array_map(function (Entity\User $user) {
                return [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                ];
            }, iterator_to_array($results));
        }

        return new Response($this->get('serializer')->serialize([
            'users' => $results,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}
