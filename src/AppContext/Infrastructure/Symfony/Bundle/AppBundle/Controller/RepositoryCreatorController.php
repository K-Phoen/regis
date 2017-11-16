<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;

class RepositoryCreatorController extends Controller
{
    public function newAction()
    {
        return $this->render('@RegisApp/Repositories/new.html.twig');
    }

    public function remoteRepositoriesListAction()
    {
        $repositories = $this->get('regis.app.remote.repositories')->forUser($this->getUser());

        return new Response($this->get('serializer')->serialize([
            'repositories' => $repositories,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function createAction(Request $request)
    {
        $command = new Command\Repository\Register(
            $this->getUser(),
            $request->request->get('type'),
            $request->request->get('identifier'),
            $request->request->get('name')
        );

        /** @var Entity\Repository $repository */
        $repository = $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Repository added.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }
}
