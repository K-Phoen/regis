<?php

namespace Tests\Functional\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RepositoriesControllerTest extends WebTestCase
{
    public function testTheRepositoriesListRequiresAuthentication()
    {
        $client = static::createClient();
        $client->request('GET', '/backend/repositories');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testThatAnAuthorizedUserCanAccessTheRepositoriesList()
    {
        $client = static::createAdminClient();
        $client->request('GET', '/backend/repositories');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    private static function createAdminClient()
    {
        return static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);
    }
}
