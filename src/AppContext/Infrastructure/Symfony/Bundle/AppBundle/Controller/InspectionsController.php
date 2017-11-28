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
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity as GhEntity;
use Regis\AppContext\Domain\Entity;

class InspectionsController extends Controller
{
    public function retryAction(GhEntity\PullRequestInspection $inspection)
    {
        /** @var Entity\Repository $repository */
        $repository = $inspection->getRepository();

        $command = new Command\Inspection\SchedulePullRequest($inspection->getPullRequest());
        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Inspection retried.');

        return $this->redirectToRoute('repositories_detail', ['id' => $repository->getId()]);
    }

    public function detailAction(Entity\Inspection $inspection)
    {
        return $this->render('@RegisApp/Inspections/detail.html.twig', [
            'inspection' => $inspection,
        ]);
    }
}
