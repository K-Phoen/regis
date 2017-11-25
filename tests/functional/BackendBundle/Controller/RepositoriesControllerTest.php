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

        $crawler = $client->request('GET', '/backend/repositories/f733e45a-6fc7-404b-879d-656d68e0498d');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('.inspections table tbody tr')->count(),
            'Some inspections are found'
        );
    }
}
