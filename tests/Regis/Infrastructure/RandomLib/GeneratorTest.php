<?php

namespace Tests\Regis\Infrastructure\RandomLib;

use PHPUnit\Framework\TestCase;
use Regis\Infrastructure\RandomLib\Generator;

class GeneratorTest extends TestCase
{
    public function testItCanGenerateRandomStrings()
    {
        $generator = new Generator();

        $this->assertInternalType('string', $result1 = $generator->randomString(24));
        $this->assertEquals(24, strlen($result1));

        $this->assertInternalType('string', $result2 = $generator->randomString(24));
        $this->assertEquals(24, strlen($result2));

        $this->assertNotEquals($result1, $result2);
    }
}
