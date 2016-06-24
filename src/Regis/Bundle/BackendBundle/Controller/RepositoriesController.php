<?php

declare(strict_types=1);

namespace Regis\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RepositoriesController extends Controller
{
    public function listAction()
    {
        $repositories = $this->get('regis.repository.repositories')->findAll();

        return $this->render('@RegisBackend/Repositories/list.html.twig', [
            'repositories' => $repositories
        ]);
    }
}