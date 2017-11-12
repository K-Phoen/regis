<?php

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

        $this->assertEquals($oldStart, $change->getRangeOldStart());
        $this->assertEquals($oldCount, $change->getRangeOldCount());
        $this->assertEquals($newStart, $change->getRangeNewStart());
        $this->assertEquals($newCount, $change->getRangeNewCount());
        $this->assertEquals($lines, $change->getLines());
    }
}
