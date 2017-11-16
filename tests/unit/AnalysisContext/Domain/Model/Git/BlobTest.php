<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Domain\Model\Git;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class BlobTest extends TestCase
{
    public function testItJustHoldsValues()
    {
        $blob = new Git\Blob('hash', 'content', 'text/plain');

        $this->assertSame('hash', $blob->getHash());
        $this->assertSame('content', $blob->getContent());
        $this->assertSame('text/plain', $blob->getMimetype());
    }
}
