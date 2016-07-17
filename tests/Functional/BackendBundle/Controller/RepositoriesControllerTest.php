<?php

namespace Tests\Functional\BackendBundle\Controller;

use Tests\Functional\WebTestCase;

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
        $this->logIn($client, 'user');

        $crawler = $client->request('GET', '/backend/repositories');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('.main:contains("github/test")')->count(),
            'A repository is found'
        );
    }

    public function testThatTheRepositoryDetailsPageIsAccessible()
    {
        $client = static::createClient();
        $this->logIn($client, 'user');

        $crawler = $client->request('GET', '/backend/repositories/github/test');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('.inspections table tbody tr')->count(),
            'Some inspections are found'
        );
    }
}
