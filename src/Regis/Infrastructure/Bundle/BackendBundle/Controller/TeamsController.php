<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Regis\Application\Command;
use Regis\Application\Spec;
use Regis\Infrastructure\Bundle\BackendBundle\Form;

class TeamsController extends Controller
{
    public function listAction()
    {
        $teams = $this->get('regis.repository.teams')->matching(new Spec\Team\AccessibleBy($this->getUser()));

        return $this->render('@RegisBackend/Teams/list.html.twig', [
            'teams' => $teams
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

        return $this->render('@RegisBackend/Teams/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}