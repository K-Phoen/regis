<?php

namespace Tests\Regis\Application\Inspection;

use Regis\Domain\Model;

abstract class InspectionTestCase extends \PHPUnit_Framework_TestCase
{
    protected function diff(array $addedPhpFiles = []): Model\Git\Diff
    {
        $diff = $this->getMockBuilder(Model\Git\Diff::class)->disableOriginalConstructor()->getMock();
        $diff->expects($this->any())
            ->method('getAddedPhpFiles')
            ->will($this->returnValue(new \ArrayIterator($addedPhpFiles )));

        return $diff;
    }

    protected function file(string $name): Model\Git\Diff\File
    {
        $diff = $this->getMockBuilder(Model\Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $diff->expects($this->any())
            ->method('getNewName')
            ->will($this->returnValue($name));
        $diff->expects($this->any())
            ->method('getNewContent')
            ->will($this->returnValue('some content'));

        return $diff;
    }
}
