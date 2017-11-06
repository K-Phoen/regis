<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Controller;

use Regis\GithubContext\Domain\Repository\Repositories;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Spec;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Form;

class RepositoriesController extends Controller
{
    public function listAction()
    {
        $repositories = $this->get('regis.github.repository.repositories')->matching(new Spec\Repository\AccessibleBy($this->getUser()));

        return $this->render('@RegisGithub/Repositories/list.html.twig', [
            'repositories' => $repositories,
        ]);
    }

    public function lastRepositoriesAction()
    {
        $repositories = $this->get('regis.github.repository.repositories')->matching(new Spec\Repository\AccessibleBy($this->getUser()));

        return $this->render('@RegisGithub/Repositories/_last_repositories.html.twig', [
            'repositories' => $repositories,
        ]);
    }

    public function detailAction($identifier)
    {
        // TODO check access rights
        $repository = $this->get('regis.github.repository.repositories')->find($identifier, Repositories::MODE_FETCH_RELATIONS);

        return $this->render('@RegisGithub/Repositories/detail.html.twig', [
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

    public function editAction(Request $request, Entity\Repository $repository)
    {
        // TODO check access rights
        $form = $form = $this->createForm(Form\EditRepositoryConfigurationType::class, $repository, [
            'action' => $this->generateUrl('repositories_edit', ['identifier' => $repository->getIdentifier()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new Command\Repository\DefineSharedSecret(
                $repository,
                $form->get('sharedSecret')->getData()
            );

            $this->get('tactician.commandbus')->handle($command);

            $this->addFlash('info', 'Repository updated.');

            return $this->redirectToRoute('repositories_list');
        }

        return $this->render('@RegisGithub/Repositories/edit.html.twig', [
            'form' => $form->createView(),
            'repository' => $repository,
        ]);
    }
}
