<?php

namespace Tests\Regis\Domain\Model\Git;

use Regis\Domain\Model\Git;

class DiffTest extends \PHPUnit_Framework_TestCase
{
    public function testHeadAndBaseAreGetFromTheRevision()
    {
        $revisions = $this->revisions();
        $revisions->expects($this->once())
            ->method('getBase')
            ->will($this->returnValue('base sha'));
        $revisions->expects($this->once())
            ->method('getHead')
            ->will($this->returnValue('head sha'));

        $diff = new Git\Diff($revisions, [], 'raw diff');

        $this->assertEquals('base sha', $diff->getBase());
        $this->assertEquals('head sha', $diff->getHead());
        $this->assertEquals('raw diff', $diff->getRawDiff());
    }

    public function testGetAddedPhpFiles()
    {
        $binaryFile = $this->binaryFile();
        $deletedFile = $this->binaryFile();
        $renamedFile = $this->renamedFile();
        $textFile = $this->textFile();
        $phpFile = $this->phpFile();
        $diff = new Git\Diff($this->revisions(), [$binaryFile, $deletedFile, $renamedFile, $textFile, $phpFile], 'raw diff');

        $this->assertEquals([$phpFile], iterator_to_array($diff->getAddedPhpFiles()));
    }

    public function testGetAddedTextFilesExcludesBinaryFiles()
    {
        $binaryFile = $this->binaryFile();
        $textFile = $this->textFile();
        $diff = new Git\Diff($this->revisions(), [$binaryFile, $textFile], 'raw diff');

        $this->assertEquals([$textFile], iterator_to_array($diff->getAddedTextFiles()));
        $this->assertEquals([$binaryFile, $textFile], $diff->getFiles());
    }

    public function testGetAddedTextFilesExcludesRenamedFiles()
    {
        $renamedFile = $this->renamedFile();
        $textFile = $this->textFile();
        $diff = new Git\Diff($this->revisions(), [$renamedFile, $textFile], 'raw diff');

        $this->assertEquals([$textFile], iterator_to_array($diff->getAddedTextFiles()));
        $this->assertEquals([$renamedFile, $textFile], $diff->getFiles());
    }

    public function testGetAddedTextFilesExcludesDeletedFiles()
    {
        $deletedFile = $this->deletedFile();
        $textFile = $this->textFile();
        $diff = new Git\Diff($this->revisions(), [$deletedFile, $textFile], 'raw diff');

        $this->assertEquals([$textFile], iterator_to_array($diff->getAddedTextFiles()));
        $this->assertEquals([$deletedFile, $textFile], $diff->getFiles());
    }

    private function revisions(): Git\Revisions
    {
        return $this->getMockBuilder(Git\Revisions::class)->disableOriginalConstructor()->getMock();
    }

    private function binaryFile(): Git\Diff\File
    {
        $file = $this->getMockBuilder(Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $file->expects($this->any())
            ->method('isBinary')
            ->will($this->returnValue(true));

        return $file;
    }

    private function renamedFile(): Git\Diff\File
    {
        $file = $this->getMockBuilder(Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $file->expects($this->any())
            ->method('isRename')
            ->will($this->returnValue(true));

        return $file;
    }

    private function deletedFile(): Git\Diff\File
    {
        $file = $this->getMockBuilder(Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $file->expects($this->any())
            ->method('isDeletion')
            ->will($this->returnValue(true));

        return $file;
    }

    private function textFile(): Git\Diff\File
    {
        return $this->getMockBuilder(Git\Diff\File::class)->disableOriginalConstructor()->getMock();
    }

    private function phpFile(): Git\Diff\File
    {
        $file = $this->getMockBuilder(Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $file->expects($this->any())
            ->method('isPhp')
            ->will($this->returnValue(true));

        return $file;
    }
}
