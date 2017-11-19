<?php

declare(strict_types=1);

namespace Tests\Functional\AppBundle\Controller;

use Tests\Functional\WebTestCase;

class AppControllerTest extends WebTestCase
{
    public function testTheHomepageCanBeDisplayed()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
