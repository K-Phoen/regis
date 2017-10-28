<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthController extends Controller
{
    public function loginAction()
    {
        return $this->render('@RegisAuth/Auth/login.html.twig');
    }

    public function githubConnectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('github')
            ->redirect($scopes = ['user:email', 'repo']);
    }

    public function githubConnectCheckAction()
    {
    }
}
