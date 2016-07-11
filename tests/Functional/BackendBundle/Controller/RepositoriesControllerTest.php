<?php

namespace Tests\Functional\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class RepositoriesControllerTest extends WebTestCase
{
    public function testTheRepositoriesListRequiresAuthentication()
    {
        $client = static::createClient();
        $client->request('GET', '/backend/repositories');

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testThatAnAuthorizedUserCanAccessTheRepositoriesList()
    {
        $client = static::createClient();
        $this->logIn($client, 'admin');
        $client->request('GET', '/backend/repositories');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    private function logIn(Client $client, string $username)
    {
        $container = $client->getContainer();
        $session = $container->get('session');
        $user = $container->get('regis.repository.users')->findByUsername($username);

        $firewall = 'main';
        $token = new PostAuthenticationGuardToken($user, 'github', $user->getRoles());

        $container->get('security.token_storage')->setToken($token);

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
