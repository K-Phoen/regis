<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
