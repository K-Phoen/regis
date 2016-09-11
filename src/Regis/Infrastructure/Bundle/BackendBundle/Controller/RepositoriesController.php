<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Regis\Domain\Repository\Repositories;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Regis\Application\Command;
use Regis\Application\Spec;
use Regis\Domain\Entity;
use Regis\Infrastructure\Bundle\BackendBundle\Form;

class RepositoriesController extends Controller
{
    public function listAction()
    {
        $repositories = $this->get('regis.repository.repositories')->matching(new Spec\Repository\AccessibleBy($this->getUser()));

        return $this->render('@RegisBackend/Repositories/list.html.twig', [
            'repositories' => $repositories
        ]);
    }

    public function lastRepositoriesAction()
    {
        $repositories = $this->get('regis.repository.repositories')->matching(new Spec\Repository\AccessibleBy($this->getUser()));

        return $this->render('@RegisBackend/Repositories/_last_repositories.html.twig', [
            'repositories' => $repositories
        ]);
    }

    public function detailAction($identifier)
    {
        // TODO check access rights
        $repository = $this->get('regis.repository.repositories')->find($identifier, Repositories::MODE_FETCH_RELATIONS);

        return $this->render('@RegisBackend/Repositories/detail.html.twig', [
            'repository' => $repository
        ]);
    }

    public function setupWebhookAction(Entity\Github\Repository $repository)
    {
        // TODO check access rights

        $absoluteUrl = $this->get('router')->generate('webhook_github', [],  UrlGeneratorInterface::ABSOLUTE_URL);

        $command = new Command\Github\Webhook\Create(
            $repository->getOwnerUsername(),
            $repository->getName(),
            $absoluteUrl
        );

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Webhook setup.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function editAction(Request $request, Entity\Github\Repository $repository)
    {
        // TODO check access rights
        $form = $form = $this->createForm(Form\EditRepositoryConfigurationType::class, $repository, [
            'action' => $this->generateUrl('repositories_edit', ['identifier' => $repository->getIdentifier()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new Command\Github\Repository\UpdateConfiguration(
                $repository,
                $form->get('sharedSecret')->getData()
            );

            $this->get('tactician.commandbus')->handle($command);

            $this->addFlash('info', 'Repository updated.');

            return $this->redirectToRoute('repositories_list');
        }

        return $this->render('@RegisBackend/Repositories/edit.html.twig', [
            'form' => $form->createView(),
            'repository' => $repository,
        ]);
    }
}