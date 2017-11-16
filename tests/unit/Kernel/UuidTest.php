<?php

declare(strict_types=1);

namespace Tests\Regis\Kernel;

use PHPUnit\Framework\TestCase;
use Regis\Kernel\Uuid;

class UuidTest extends TestCase
{
    public function testItGeneratesSomethingThatLooksLikeAUuid()
    {
        $this->assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', Uuid::create());
    }
}
