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

            // f733e45a-6fc7-404b-879d-656d68e0498d is K-Phoen/regis
            ['K-P', ['f733e45a-6fc7-404b-879d-656d68e0498d']],
            ['k-phoe', ['f733e45a-6fc7-404b-879d-656d68e0498d']],
            ['regis', ['f733e45a-6fc7-404b-879d-656d68e0498d']],
        ];
    }
}
