<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthController extends Controller
{
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
