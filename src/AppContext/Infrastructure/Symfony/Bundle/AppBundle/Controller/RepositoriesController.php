<?php

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

    public function detailAction($identifier)
    {
        // TODO check access rights
        $repository = $this->get('regis.app.repository.repositories')->find($identifier, Repositories::MODE_FETCH_RELATIONS);

        return $this->render('@RegisApp/Repositories/detail.html.twig', [
            'repository' => $repository,
        ]);
    }

    public function setupWebhookAction(Entity\Repository $repository)
    {
        // TODO check access rights

        $absoluteUrl = $this->get('router')->generate('github_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $command = new Command\Repository\CreateWebhook($repository->toIdentifier(), $absoluteUrl);

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Webhook setup.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function disableInspectionsAction(Entity\Repository $repository)
    {
        $command = new Command\Repository\DisableInspections($repository);

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Inspections disabled.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function enableInspectionsAction(Entity\Repository $repository)
    {
        $command = new Command\Repository\EnableInspections($repository);

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Inspections enabled.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }
}
