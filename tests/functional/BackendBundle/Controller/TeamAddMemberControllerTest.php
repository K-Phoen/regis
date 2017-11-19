<?php

declare(strict_types=1);

namespace Tests\Functional\BackendBundle\Controller;

use Tests\Functional\WebTestCase;

class TeamAddMemberControllerTest extends WebTestCase
{
    public function testUsersCanNotBeSearchedByAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/backend/teams/users');

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    /**
     * @dataProvider searchProvider
     */
    public function testUserCanBeSearched($search, $expectedUserIds)
    {
        $client = static::createClient();
        $this->logIn($client, 'K-Phoen');

        $client->request('GET', '/backend/teams/users', [
            'q' => $search,
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame('application/json', $client->getResponse()->headers->get('Content-Type'));

        $results = json_decode($client->getResponse()->getContent(), true);
        $emails = array_column($results['users'], 'id');

        $this->assertSame($expectedUserIds, $emails);
    }

    public function searchProvider()
    {
        return [
            ['', []],
            ['joe', []],

            ['K-Ph', ['d67ff369-704b-4315-a75f-b67f5bc9cc5a']],
            ['k-pho', ['d67ff369-704b-4315-a75f-b67f5bc9cc5a']],
        ];
    }
}
