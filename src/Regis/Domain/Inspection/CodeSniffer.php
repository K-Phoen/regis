<?php

declare(strict_types=1);

namespace Regis\Domain\Inspection;

use Regis\Domain\Model\Violation;
use Symfony\Component\Process\Process;

use Regis\Domain\Inspection;
use Regis\Domain\Model\Git as Model;
use Regis\Domain\Reporter;

class CodeSniffer implements Inspection
{
    private $phpcsBin;

    public function __construct(string $phpCsBin)
    {
        $this->phpcsBin = $phpCsBin;
    }

    public function inspectDiff(Model\Diff $diff): \Traversable
    {
        /** @var Model\Diff\File $file */
        foreach ($diff->getFiles() as $file) {
            if ($file->isBinary() || $file->isRename() || $file->isDeletion()) {
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

    private function buildViolations(Model\Diff\File $file, array $report): \Traversable
    {
        foreach ($report['messages'] as $message) {
            try {
                $position = $this->findPositionForLine($message['line'], $file);
            } catch (\Exception $e) {
                continue;
            }

            yield new Violation($file->getNewName(), $position, $message['message']);
        }
    }

    private function executePhpCs(string $fileName, string $fileContent): array
    {
        $process = new Process(sprintf('%s --report=json --stdin-path=%s', escapeshellarg($this->phpcsBin), escapeshellarg($fileName)));
        $process->setInput($fileContent);
        $process->run();

        return json_decode($process->getOutput(), true);
    }

    private function findPositionForLine(int $line, Model\Diff\File $file): int
    {
        $changes = $file->getChanges();

        /** @var Model\Diff\Change $change */
        foreach ($changes as $change) {
            $rangeStart = $change->getRangeNewStart() - 1;

            /** @var Model\Diff\Line $diffLine */
            foreach ($change->getAddedLines() as $diffLine) {
                if ($rangeStart + $diffLine->getPosition() === $line) {
                    return $diffLine->getPosition();
                }
            }
        }

        // TODO specific exception
        throw new \RuntimeException('Unable to find change for line '.$line);
    }
}