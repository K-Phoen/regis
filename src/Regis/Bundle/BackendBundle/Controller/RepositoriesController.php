<?php

declare(strict_types=1);

namespace Regis\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Regis\Application\Model;
use Regis\Bundle\BackendBundle\Form;
use Symfony\Component\HttpFoundation\Request;

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
            $repository = new Model\Repository($form->get('identifier')->getData(), $form->get('sharedSecret')->getData());
            $this->get('regis.repository.repositories')->save($repository);

            $this->addFlash('info', 'Repository added.');

            return $this->redirectToRoute('repositories_list');
        }

        return $this->render('@RegisBackend/Repositories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}