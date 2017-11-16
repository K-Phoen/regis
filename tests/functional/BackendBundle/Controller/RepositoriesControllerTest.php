<?php

declare(strict_types=1);

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
        $this->logIn($client, 'K-Phoen');

        $crawler = $client->request('GET', '/backend/repositories');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('.main:contains("K-Phoen/regis")')->count(),
            'A repository is found'
        );
    }

    public function testThatTheRepositoryDetailsPageIsAccessible()
    {
        $client = static::createClient();
        $this->logIn($client, 'K-Phoen');

        $crawler = $client->request('GET', '/backend/repositories/K-Phoen/regis');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('.inspections table tbody tr')->count(),
            'Some inspections are found'
        );
    }
}
