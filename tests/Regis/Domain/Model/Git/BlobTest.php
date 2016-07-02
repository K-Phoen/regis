<?php

namespace Tests\Regis\Domain\Model\Git;

use Regis\Domain\Model\Git;

class BlobTest extends \PHPUnit_Framework_TestCase
{
    public function testItJustHoldsValues()
    {
        $blob = new Git\Blob('hash', 'content', 'text/plain');

        $this->assertEquals('hash', $blob->getHash());
        $this->assertEquals('content', $blob->getContent());
        $this->assertEquals('text/plain', $blob->getMimetype());
    }
}
