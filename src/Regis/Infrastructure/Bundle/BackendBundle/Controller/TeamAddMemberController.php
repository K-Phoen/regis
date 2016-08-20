<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Regis\Application\Command;
use Regis\Application\Spec;
use Regis\Domain\Entity;

class TeamAddMemberController extends Controller
{
    /**
     * TODO improve UI
     */
    public function addMemberAction(Entity\Team $team)
    {
        return $this->render('@RegisBackend/Teams/add_member.html.twig', [
            'team' => $team,
        ]);
    }

    public function submitMembershipAction(Request $request, Entity\Team $team)
    {
        $command = new Command\Team\AddMember($team, $request->request->get('new_member_id'));

        $this->get('tactician.commandbus')->handle($command);
        $this->addFlash('info', 'Done.');

        return $this->redirectToRoute('teams_list');
    }

    public function userSearchAction(Request $request)
    {
        $q = $request->query->get('q');

        // TODO should be in a command
        // TODO verify authorizations
        if (empty($q)) {
            $results = [];
        } else {
            $results = $this->get('regis.repository.users')->matching(new Spec\User\Matches($q));
            // TODO eurk
            $results = array_map(function(Entity\User $user) {
                return [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                ];
            }, iterator_to_array($results));
        }

        return new Response($this->get('serializer')->serialize([
            'users' => $results,
        ], 'json'), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}