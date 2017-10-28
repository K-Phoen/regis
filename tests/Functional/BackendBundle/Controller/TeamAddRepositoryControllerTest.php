<?php

namespace Tests\Functional\BackendBundle\Controller;

use Tests\Functional\WebTestCase;

class TeamAddRepositoryControllerTest extends WebTestCase
{
    public function testRepositoriesCanNotBeSearchedByAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/backend/teams/repositories');

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    /**
     * @dataProvider searchProvider
     */
    public function testRepositoriesCanBeSearched($search, $expectedRepoIdentifiers)
    {
        $client = static::createClient();
        $this->logIn($client, 'user');

        $client->request('GET', '/backend/teams/repositories', [
            'q' => $search,
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));

        $results = json_decode($client->getResponse()->getContent(), true);
        $identifiers = array_column($results['repositories'], 'identifier');

        $this->assertEquals($expectedRepoIdentifiers, $identifiers);
    }

    public function searchProvider()
    {
        return [
            ['', []],
            ['github', ['github/test']],
            ['git', ['github/test']],
            ['test', ['github/test']],
        ];
    }
}
