<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Spec;
use Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Form;

class TeamsController extends Controller
{
    public function listAction()
    {
        $teams = $this->get('regis.github.repository.teams')->matching(new Spec\Team\AccessibleBy($this->getUser()));

        return $this->render('@RegisGithub/Teams/list.html.twig', [
            'teams' => $teams,
        ]);
    }

    public function createAction(Request $request)
    {
        $form = $form = $this->createForm(Form\CreateTeamType::class, null, [
            'action' => $this->generateUrl('teams_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new Command\Team\Create(
                $this->getUser(),
                $form->get('name')->getData()
            );

            $this->get('tactician.commandbus')->handle($command);

            $this->addFlash('info', 'Team created.');

            return $this->redirectToRoute('teams_list');
        }

        return $this->render('@RegisGithub/Teams/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
