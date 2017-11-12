<?php

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
