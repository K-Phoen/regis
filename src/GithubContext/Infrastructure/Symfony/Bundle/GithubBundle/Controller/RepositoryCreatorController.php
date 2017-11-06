<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;

class RepositoryCreatorController extends Controller
{
    public function newAction()
    {
        return $this->render('@RegisGithub/Repositories/new.html.twig');
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
        $command = new Command\Repository\RegisterRepository($this->getUser(), $request->request->get('identifier'));

        /** @var Entity\Repository $repository */
        $repository = $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Repository added.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }
}
