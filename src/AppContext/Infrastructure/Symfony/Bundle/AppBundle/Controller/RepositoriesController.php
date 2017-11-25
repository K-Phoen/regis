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

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\Spec;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository\Repositories;

class RepositoriesController extends Controller
{
    public function listAction()
    {
        $repositories = $this->get('regis.app.repository.repositories')->matching(new Spec\Repository\AccessibleBy($this->getUser()));

        return $this->render('@RegisApp/Repositories/list.html.twig', [
            'repositories' => $repositories,
        ]);
    }

    public function lastRepositoriesAction()
    {
        $repositories = $this->get('regis.app.repository.repositories')->matching(new Spec\Repository\AccessibleBy($this->getUser()));

        return $this->render('@RegisApp/Repositories/_last_repositories.html.twig', [
            'repositories' => $repositories,
        ]);
    }

    public function detailAction(string $id)
    {
        // TODO check access rights
        $repository = $this->get('regis.app.repository.repositories')->find($id, Repositories::MODE_FETCH_RELATIONS);

        return $this->render('@RegisApp/Repositories/detail.html.twig', [
            'repository' => $repository,
        ]);
    }

    public function setupWebhookAction(Entity\Repository $repository)
    {
        // TODO check access rights

        $absoluteUrl = $this->generateUrl($repository->getType().'_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->get('tactician.commandbus')->handle(new Command\Remote\CreateWebhook($repository, $absoluteUrl));

        $this->addFlash('info', 'Webhook setup.');

        return $this->redirectToRoute('repositories_detail', ['id' => $repository->getId()]);
    }

    public function disableInspectionsAction(Entity\Repository $repository)
    {
        $this->get('tactician.commandbus')->handle(new Command\Repository\DisableInspections($repository));

        $this->addFlash('info', 'Inspections disabled.');

        return $this->redirectToRoute('repositories_detail', ['id' => $repository->getId()]);
    }

    public function enableInspectionsAction(Entity\Repository $repository)
    {
        $this->get('tactician.commandbus')->handle(new Command\Repository\EnableInspections($repository));

        $this->addFlash('info', 'Inspections enabled.');

        return $this->redirectToRoute('repositories_detail', ['id' => $repository->getId()]);
    }
}
