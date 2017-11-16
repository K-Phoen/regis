<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Application\Random;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Random\Generator;

class GeneratorTest extends TestCase
{
    public function testItCanGenerateRandomStrings()
    {
        $generator = new Generator();

        $this->assertInternalType('string', $result1 = $generator->randomString(24));
        $this->assertSame(24, strlen($result1));

        $this->assertInternalType('string', $result2 = $generator->randomString(24));
        $this->assertSame(24, strlen($result2));

        $this->assertNotSame($result1, $result2);
    }
}
