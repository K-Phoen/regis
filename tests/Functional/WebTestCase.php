<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

use Regis\Application\Spec;

abstract class WebTestCase extends BaseTestCase
{
    protected function logIn(Client $client, string $username)
    {
        $container = $client->getContainer();
        $session = $container->get('session');
        $user = current($container->get('regis.repository.users')->matching(new Spec\User\Named($username)));

        $firewall = 'main';
        $token = new PostAuthenticationGuardToken($user, 'github', $user->getRoles());

        $container->get('security.token_storage')->setToken($token);

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
