<?php

namespace Tests\Regis\Application\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\Domain\Model;

abstract class InspectionTestCase extends TestCase
{
    protected function diff(array $addedPhpFiles = []): Model\Git\Diff
    {
        $diff = $this->getMockBuilder(Model\Git\Diff::class)->disableOriginalConstructor()->getMock();
        $diff->method('getAddedPhpFiles')->willReturn(new \ArrayIterator($addedPhpFiles));

        return $diff;
    }

    protected function file(string $name): Model\Git\Diff\File
    {
        $diff = $this->getMockBuilder(Model\Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $diff->method('getNewName')->willReturn($name);
        $diff->method('getNewContent')->willReturn('some content');

        return $diff;
    }
}
