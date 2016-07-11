<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Regis\Application\Command;
use Regis\Domain\Entity;
use Regis\Infrastructure\Bundle\BackendBundle\Form;
use Symfony\Component\HttpFoundation\Response;

class RepositoryCreatorController extends Controller
{
    public function newAction(Request $request)
    {
        $form = $form = $this->createForm(Form\NewRepositoryType::class, null, [
            'action' => $this->generateUrl('repositories_new'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new Command\Github\Repository\Create(
                $this->getUser(),
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

    public function remoteRepositoriesListAction()
    {
        $githubClient = $this->get('regis.github.client_factory')->createForUser($this->getUser());
        $repositories = $githubClient->listRepositories();

        return new Response($this->get('serializer')->serialize([
            'repositories' => $repositories,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function createAction(Request $request)
    {
        $command = new Command\Github\Repository\Create($this->getUser(), $request->request->get('identifier'));

        /** @var Entity\Github\Repository $repository */
        $repository = $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Repository added.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }
}