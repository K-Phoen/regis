<?php

declare(strict_types=1);

namespace Regis\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Regis\Application\Entity;
use Regis\Bundle\BackendBundle\Form;

class RepositoriesController extends Controller
{
    public function listAction()
    {
        $repositories = $this->get('regis.repository.repositories')->findAll();

        return $this->render('@RegisBackend/Repositories/list.html.twig', [
            'repositories' => $repositories
        ]);
    }

    public function newAction(Request $request)
    {
        $form = $form = $this->createForm(Form\NewRepositoryType::class, null, [
            'action' => $this->generateUrl('repositories_new'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Entity\Repository $repository */
            $repository = $form->getData();
            $this->get('regis.repository.repositories')->save($repository);

            $this->addFlash('info', 'Repository added.');

            return $this->redirectToRoute('repositories_list');
        }

        return $this->render('@RegisBackend/Repositories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, Entity\Repository $repository)
    {
        $form = $form = $this->createForm(Form\EditRepositoryType::class, $repository, [
            'action' => $this->generateUrl('repositories_edit', ['identifier' => $repository->getIdentifier()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Entity\Repository $repository */
            $repository = $form->getData();
            $this->get('regis.repository.repositories')->save($repository);

            $this->addFlash('info', 'Repository updated.');

            return $this->redirectToRoute('repositories_list');
        }

        return $this->render('@RegisBackend/Repositories/edit.html.twig', [
            'form' => $form->createView(),
            'repository' => $repository,
        ]);
    }
}