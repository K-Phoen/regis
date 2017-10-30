<?php

namespace Tests\Regis\Domain\Model\Git;

use PHPUnit\Framework\TestCase;
use Regis\Domain\Model\Git;

class RevisionsTest extends TestCase
{
    public function testItCanBeConstructedFromAnArray()
    {
        $revisions = Git\Revisions::fromArray([
            'base' => 'base sha',
            'head' => 'head sha',
        ]);

        $this->assertEquals('base sha', $revisions->getBase());
        $this->assertEquals('head sha', $revisions->getHead());
    }

    public function testItCanBeTransformedToAnArray()
    {
        $revisions = new Git\Revisions('base sha', 'head sha');
        $data = $revisions->toArray();

        $this->assertEquals('base sha', $data['base']);
        $this->assertEquals('head sha', $data['head']);
    }
}
