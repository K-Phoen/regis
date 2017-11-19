<?php

declare(strict_types=1);

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
        $this->logIn($client, 'K-Phoen');

        $client->request('GET', '/backend/teams/repositories', [
            'q' => $search,
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame('application/json', $client->getResponse()->headers->get('Content-Type'));

        $results = json_decode($client->getResponse()->getContent(), true);
        $identifiers = array_column($results['repositories'], 'identifier');

        $this->assertSame($expectedRepoIdentifiers, $identifiers);
    }

    public function searchProvider()
    {
        return [
            ['', []],
            ['no-match', []],

            ['K-P', ['K-Phoen/regis']],
            ['k-phoe', ['K-Phoen/regis']],
            ['regis', ['K-Phoen/regis']],
        ];
    }
}
