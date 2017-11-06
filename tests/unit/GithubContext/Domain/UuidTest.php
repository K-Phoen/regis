<?php

namespace Tests\Regis\GithubContext\Domain;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Uuid;

class UuidTest extends TestCase
{
    public function testItGeneratesSomethingThatLooksLikeAUuid()
    {
        $this->assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', Uuid::create());
    }
}
