<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Regis\AppContext\Application\Spec;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;

class TeamAddRepositoryController extends Controller
{
    /**
     * TODO improve UI
     */
    public function addRepositoryAction(Entity\Team $team)
    {
        return $this->render('@RegisApp/Teams/add_repository.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * TODO check authorizations
     */
    public function submitRepositoryAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\AddRepository($team, $request->request->get('new_repository_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    /**
     * TODO check authorizations
     */
    public function removeRepositoryAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\RemoveRepository($team, $request->request->get('repository_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function repositorySearchAction(Request $request)
    {
        $q = $request->query->get('q');

        // TODO should be in a command?
        // TODO verify authorizations
        if (empty($q)) {
            $results = [];
        } else {
            $results = $this->get('regis.github.repository.repositories')->matching(new Spec\Repository\Matches($q));
            // TODO eurk
            $results = array_map(function (Entity\Repository $repo) {
                return [
                    'identifier' => $repo->getIdentifier(),
                ];
            }, iterator_to_array($results));
        }

        return new Response($this->get('serializer')->serialize([
            'repositories' => $results,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}
