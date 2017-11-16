<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Domain\Model\Git\Diff;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class ChangeTest extends TestCase
{
    public function testItJustHoldsValues()
    {
        $change = new Git\Diff\Change(
            $oldStart = 1,
            $oldCount = 2,
            $newStart = 3,
            $newCount = 4,
            $lines = []
        );

        $this->assertSame($oldStart, $change->getRangeOldStart());
        $this->assertSame($oldCount, $change->getRangeOldCount());
        $this->assertSame($newStart, $change->getRangeNewStart());
        $this->assertSame($newCount, $change->getRangeNewCount());
        $this->assertSame($lines, $change->getLines());
    }
}
