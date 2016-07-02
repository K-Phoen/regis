<?php

namespace Tests\Regis\Domain;

use Regis\Domain\Uuid;

class UuidTest extends \PHPUnit_Framework_TestCase
{
    public function testItGeneratesSomethingThatLooksLikeAUuid()
    {
        $this->assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', Uuid::create());
    }
}
