<?php

namespace Regis\Domain\Inspection;

use Gitonomy\Git as Gitonomy;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Diff\FileChange;
use Symfony\Component\Process\Process;

use Regis\Domain\Inspection;
use Regis\Domain\Model;
use Regis\Domain\Reporter;

class CodeSniffer implements Inspection
{
    private $phpcsBin;

    public function __construct(string $phpCsBin)
    {
        $this->phpcsBin = $phpCsBin;
    }

    public function inspectDiff(Gitonomy\Diff\Diff $diff): \Traversable
    {
        /** @var File $file */
        foreach ($diff->getFiles() as $file) {
            if ($file->isBinary() || $file->isRename()) {
                continue;
            }

            $fileName = $file->getNewName();
            $report = $this->executePhpCs($fileName, $file->getNewBlob()->getContent());

            if (empty($report['files'][$fileName])) {
                continue;
            }

            yield from $this->buildViolations($file, $report['files'][$fileName]);
        }
    }

    private function buildViolations(File $file, array $report): \Traversable
    {
        foreach ($report['messages'] as $message) {
            try {
                $position = $this->findPositionForLine($message['line'], $file);
            } catch (\Exception $e) {
                continue;
            }

            yield new Model\Violation($file->getNewName(), $position, $message['message']);
        }
    }

    private function executePhpCs(string $fileName, string $fileContent): array
    {
        $process = new Process(sprintf('%s --report=json --stdin-path=%s', escapeshellarg($this->phpcsBin), escapeshellarg($fileName)));
        $process->setInput($fileContent);
        $process->run();

        return json_decode($process->getOutput(), true);
    }

    private function findPositionForLine(int $line, File $file): int
    {
        $changes = $file->getChanges();

        /** @var FileChange $change */
        foreach ($changes as $change) {
            $rangeStart = $change->getRangeNewStart() - 1;

            foreach ($change->getLines() as $i => $diffLine) {

                if ($diffLine[0] !== FileChange::LINE_ADD) {
                    continue;
                }

                $diffPosition = $i + 1;

                if ($rangeStart + $diffPosition === $line) {
                    return $diffPosition;
                }
            }
        }

        // TODO specific exception
        throw new \RuntimeException('Unable to find change for line '.$line);
    }
}