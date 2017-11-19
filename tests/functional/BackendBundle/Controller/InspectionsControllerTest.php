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

use Symfony\Bundle\FrameworkBundle\Client;
use Regis\GithubContext\Domain\Entity;
use Tests\Functional\WebTestCase;

class InspectionsControllerTest extends WebTestCase
{
    public function testViewingAnInspectionRequiresAuthentication()
    {
        $client = static::createClient();
        $inspection = $this->findInspection($client);

        $client->request('GET', '/backend/inspections/'.$inspection->getId());

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testThatAnAuthorizedUserCanAccessAnInspectionPage()
    {
        $client = static::createClient();
        $inspection = $this->findInspection($client);

        $this->logIn($client, 'K-Phoen');

        $crawler = $client->request('GET', '/backend/inspections/'.$inspection->getId());

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('.main:contains("Inspection")')->count()
        );
    }

    private function findInspection(Client $client): Entity\Inspection
    {
        $repo = $client->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository(Entity\Inspection::class);

        return $repo->findOneBy([]);
    }
}
