<?php

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

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function detailAction(Entity\Inspection $inspection)
    {
        return $this->render('@RegisApp/Inspections/detail.html.twig', [
            'inspection' => $inspection,
        ]);
    }
}
