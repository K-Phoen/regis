<?php

namespace Tests\Regis\Domain\Model\Git\Diff;

use Regis\Domain\Model\Git;

class ChangeTest extends \PHPUnit_Framework_TestCase
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
