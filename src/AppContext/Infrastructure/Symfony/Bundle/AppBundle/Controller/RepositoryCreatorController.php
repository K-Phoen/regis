<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Entity as BitbucketEntity;

class RepositoryCreatorController extends Controller
{
    public function newAction()
    {
        return $this->render('@RegisApp/Repositories/new.html.twig');
    }

    public function remoteRepositoriesListAction()
    {
        //$githubClient = $this->get('regis.github.client_factory')->createForUser($this->getGithubUser());
        $bitbucketClient = $this->get('regis.bitbucket.client_factory')->createForUser($this->getBitbucketUser());
        //$repositories = $githubClient->listRepositories();
        $repositories = $bitbucketClient->listRepositories();

        return new Response($this->get('serializer')->serialize([
            'repositories' => $repositories,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function createAction(Request $request)
    {
        $command = new Command\Repository\RegisterRepository($this->getGithubUser(), $request->request->get('identifier'));

        /** @var Entity\Repository $repository */
        $repository = $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Repository added.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    private function getGithubUser(): Entity\GithubDetails
    {
        return $this->get('regis.github.repository.users')->findByAccountId($this->getUser()->accountId());
    }

    private function getBitbucketUser(): BitbucketEntity\BitbucketDetails
    {
        return $this->get('regis.bitbucket.repository.users')->findByAccountId($this->getUser()->accountId());
    }
}
