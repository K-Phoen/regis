<?php

namespace Tests\Regis\Domain\Model;

use Regis\Domain\Model\Git\Diff\Change;
use Regis\Domain\Model\Git\Diff\Line;

class LineTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $line = new Line(Change::LINE_ADD, 2, 42, 'line content');

        $this->assertTrue($line->isAddition());
        $this->assertFalse($line->isDeletion());
        $this->assertFalse($line->isContext());
        $this->assertEquals(2, $line->getPosition());
        $this->assertEquals(42, $line->getNumber());
        $this->assertEquals(Change::LINE_ADD, $line->getChangeType());
        $this->assertEquals('line content', $line->getContent());
    }

    public function testDeletedLine()
    {
        $line = new Line(Change::LINE_REMOVE, 2, 42, 'line content');

        $this->assertTrue($line->isDeletion());
        $this->assertFalse($line->isContext());
        $this->assertFalse($line->isAddition());
        $this->assertEquals(Change::LINE_REMOVE, $line->getChangeType());
    }

    public function testContextLine()
    {
        $line = new Line(Change::LINE_CONTEXT, 2, 42, 'line content');

        $this->assertTrue($line->isContext());
        $this->assertFalse($line->isAddition());
        $this->assertFalse($line->isDeletion());
        $this->assertEquals(Change::LINE_CONTEXT, $line->getChangeType());
    }
}
