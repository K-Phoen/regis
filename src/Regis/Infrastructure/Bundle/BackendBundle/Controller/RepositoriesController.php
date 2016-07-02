<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Regis\Application\Command;
use Regis\Application\Entity;
use Regis\Infrastructure\Bundle\BackendBundle\Form;

class RepositoriesController extends Controller
{
    public function listAction()
    {
        $repositories = $this->get('regis.repository.repositories')->findAll();

        return $this->render('@RegisBackend/Repositories/list.html.twig', [
            'repositories' => $repositories
        ]);
    }

    public function detailAction(Entity\Repository $repository)
    {
        return $this->render('@RegisBackend/Repositories/detail.html.twig', [
            'repository' => $repository
        ]);
    }

    public function setupWebhookAction(Entity\Repository $repository)
    {
        $absoluteUrl = $this->get('router')->generate('webhook_github', [],  UrlGeneratorInterface::ABSOLUTE_URL);

        $command = new Command\Github\Webhook\Create(
            $repository->getOwner(),
            $repository->getName(),
            $absoluteUrl
        );

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Webhook setup.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function newAction(Request $request)
    {
        $form = $form = $this->createForm(Form\NewRepositoryType::class, null, [
            'action' => $this->generateUrl('repositories_new'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new Command\Github\Repository\Create(
                $form->get('identifier')->getData(),
                $form->get('sharedSecret')->getData()
            );

            $this->get('tactician.commandbus')->handle($command);

            $this->addFlash('info', 'Repository added.');

            return $this->redirectToRoute('repositories_list');
        }

        return $this->render('@RegisBackend/Repositories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, Entity\Github\Repository $repository)
    {
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