<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthController extends Controller
{
    public function connectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('bitbucket')
            ->redirect($scopes = ['repository', 'repository:admin']);
    }

    public function connectCheckAction()
    {
    }
}
