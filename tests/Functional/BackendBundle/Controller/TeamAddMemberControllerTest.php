<?php

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
    public function testUserCanBeSearched($search, $expectedUserEmails)
    {
        $client = static::createClient();
        $this->logIn($client, 'user');

        $client->request('GET', '/backend/teams/users', [
            'q' => $search,
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));

        $results = json_decode($client->getResponse()->getContent(), true);
        $emails = array_column($results['users'], 'email');

        $this->assertEquals($expectedUserEmails, $emails);
    }

    public function searchProvider()
    {
        return [
            ['', []],
            ['admin', ['admin@admin']],
            ['ad', ['admin@admin']],

            ['us', ['user@foo.org']],
            ['foo.org', ['user@foo.org']],
        ];
    }
}
