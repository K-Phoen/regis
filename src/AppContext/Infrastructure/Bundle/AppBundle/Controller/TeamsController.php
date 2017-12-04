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
use Regis\AppContext\Application\Spec;
use Regis\AppContext\Infrastructure\Bundle\AppBundle\Form;
use Regis\AppContext\Application\Command;

class TeamsController extends Controller
{
    public function listAction()
    {
        $teams = $this->get('regis.app.repository.teams')->matching(new Spec\Team\AccessibleBy($this->getUser()));

        return $this->render('@RegisApp/Teams/list.html.twig', [
            'teams' => $teams,
        ]);
    }

    public function createAction(Request $request)
    {
        $form = $form = $this->createForm(Form\CreateTeamType::class, null, [
            'action' => $this->generateUrl('teams_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new Command\Team\Create(
                $this->getUser(),
                $form->get('name')->getData()
            );

            $this->get('tactician.commandbus')->handle($command);

            $this->addFlash('info', 'Team created.');

            return $this->redirectToRoute('teams_list');
        }

        return $this->render('@RegisApp/Teams/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
