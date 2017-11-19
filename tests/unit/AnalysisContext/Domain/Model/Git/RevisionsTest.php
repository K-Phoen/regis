<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Domain\Model\Git;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class RevisionsTest extends TestCase
{
    public function testItCanBeConstructedFromAnArray()
    {
        $revisions = Git\Revisions::fromArray([
            'base' => 'base sha',
            'head' => 'head sha',
        ]);

        $this->assertSame('base sha', $revisions->getBase());
        $this->assertSame('head sha', $revisions->getHead());
    }

    public function testItCanBeTransformedToAnArray()
    {
        $revisions = new Git\Revisions('base sha', 'head sha');
        $data = $revisions->toArray();

        $this->assertSame('base sha', $data['base']);
        $this->assertSame('head sha', $data['head']);
    }
}
