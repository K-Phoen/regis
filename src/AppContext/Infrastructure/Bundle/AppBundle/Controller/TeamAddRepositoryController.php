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

class TeamAddRepositoryController extends Controller
{
    /**
     * TODO improve UI
     */
    public function addRepositoryAction(Entity\Team $team)
    {
        return $this->render('@RegisApp/Teams/add_repository.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * TODO check authorizations
     */
    public function submitRepositoryAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\AddRepository($team, $request->request->get('new_repository_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function removeRepositoryAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\RemoveRepository($team, $request->request->get('repository_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function repositorySearchAction(Request $request)
    {
        $q = $request->query->get('q');

        // TODO should be in a command?
        // TODO verify authorizations
        if (empty($q)) {
            $results = [];
        } else {
            $results = $this->get('regis.app.repository.repositories')->matching(new Spec\Repository\Matches($q));
            // TODO eurk
            $results = array_map(function (Entity\Repository $repo) {
                return [
                    'identifier' => $repo->getId(),
                    'name' => $repo->getName(),
                ];
            }, iterator_to_array($results));
        }

        return new Response($this->get('serializer')->serialize([
            'repositories' => $results,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}
