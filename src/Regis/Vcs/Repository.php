<?php

declare(strict_types=1);

namespace Regis\Vcs;

use Gitonomy\Git as Gitonomy;

use Regis\Application\Model\Git as Model;

class Repository
{
    /** @var Gitonomy\Repository */
    private $repository;

    public function __construct(Gitonomy\Repository $repository)
    {
        $this->repository = $repository;
    }
    
    public function update()
    {
        $this->repository->run('fetch');
    }

    public function getDiff(string $base, string $head): Model\Diff
    {
        $gitDiff = $this->repository->getDiff(sprintf('%s..%s', $base, $head));

        return new Model\Diff($base, $head, array_map(function(Gitonomy\Diff\File $file) {
            return $this->convertDiffFile($file);
        }, $gitDiff->getFiles()));
    }

    private function convertDiffFile(Gitonomy\Diff\File $file): Model\Diff\File
    {
        $blob = $this->convertBlob($file->getNewBlob());
        $changes = array_map(function(Gitonomy\Diff\FileChange $change) {
            return $this->convertChange($change);
        }, $file->getChanges());

        return new Model\Diff\File($file->getOldName(), $file->getNewName(), $file->isBinary(), $blob, $changes);
    }

    private function convertBlob(Gitonomy\Blob $blob): Model\Blob
    {
        return new Model\Blob($blob->getHash(), $blob->getContent(), $blob->getMimetype());
    }

    private function convertChange(Gitonomy\Diff\FileChange $change): Model\Diff\Change
    {
        $lines = [];
        foreach ($change->getLines() as $i => $line) {
            $lines[] = $this->convertChangeLine($i + 1, $line);
        }
            
        return new Model\Diff\Change(
            (int) $change->getRangeOldStart(),
            (int) $change->getRangeOldCount(),
            (int) $change->getRangeNewStart(),
            (int) $change->getRangeNewCount(),
            $lines
        );
    }

    private function convertChangeLine(int $position, array $line): Model\Diff\Line
    {
        return new Model\Diff\Line($line[0], $position, $line[1]);
    }
}