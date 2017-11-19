<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Domain\Model\Git\Diff;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git\Diff\Change;
use Regis\AnalysisContext\Domain\Model\Git\Diff\Line;

class LineTest extends TestCase
{
    public function testConstruction()
    {
        $line = new Line(Change::LINE_ADD, 2, 42, 'line content');

        $this->assertTrue($line->isAddition());
        $this->assertFalse($line->isDeletion());
        $this->assertFalse($line->isContext());
        $this->assertSame(2, $line->getPosition());
        $this->assertSame(42, $line->getNumber());
        $this->assertSame(Change::LINE_ADD, $line->getChangeType());
        $this->assertSame('line content', $line->getContent());
    }

    public function testDeletedLine()
    {
        $line = new Line(Change::LINE_REMOVE, 2, 42, 'line content');

        $this->assertTrue($line->isDeletion());
        $this->assertFalse($line->isContext());
        $this->assertFalse($line->isAddition());
        $this->assertSame(Change::LINE_REMOVE, $line->getChangeType());
    }

    public function testContextLine()
    {
        $line = new Line(Change::LINE_CONTEXT, 2, 42, 'line content');

        $this->assertTrue($line->isContext());
        $this->assertFalse($line->isAddition());
        $this->assertFalse($line->isDeletion());
        $this->assertSame(Change::LINE_CONTEXT, $line->getChangeType());
    }
}
