<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\Repository;
use Regis\GithubContext\Domain\Model\PullRequest;

class InspectionsController extends Controller
{
    public function retryAction(Entity\PullRequestInspection $inspection)
    {
        /** @var Entity\Repository $repository */
        $repository = $inspection->getRepository();

        // TODO we should just give the repository identifier and the command should call github to retrieve the missing
        // clone and public URLs
        $command = new Command\Inspection\SchedulePullRequest(new PullRequest(
            new Repository($repository->toIdentifier(), 'we don\'t have the clone URL, lets hope it is already cloned by now.', 'lala'),
            $inspection->getPullRequestNumber(),
            $inspection->getHead(),
            $inspection->getBase()
        ));

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Inspection retried.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function detailAction(Entity\PullRequestInspection $inspection)
    {
        return $this->render('@RegisBackend/Inspections/detail.html.twig', [
            'inspection' => $inspection,
        ]);
    }
}
