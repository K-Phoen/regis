<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

use Regis\Application\Inspection;
use Regis\Application\Model\Exception\LineNotInDiff;
use Regis\Application\Model\Git as Model;
use Regis\Application\Model\Violation;
use Regis\CodeSniffer\CodeSniffer as CodeSnifferRunner;

class CodeSniffer implements Inspection
{
    private $codeSniffer;

    public function __construct(CodeSnifferRunner $codeSniffer)
    {
        $this->codeSniffer = $codeSniffer;
    }

    public function inspectDiff(Model\Diff $diff): \Traversable
    {
        /** @var Model\Diff\File $file */
        foreach ($diff->getAddedTextFiles() as $file) {
            $report = $this->codeSniffer->execute($file->getNewName(), $file->getNewBlob()->getContent());

            foreach ($report['files'] as $fileReport) {
                yield from $this->buildViolations($file, $fileReport);
            }
        }
    }

    private function buildViolations(Model\Diff\File $file, array $report): \Traversable
    {
        foreach ($report['messages'] as $message) {
            try {
                $position = $file->findPositionForLine($message['line']);
            } catch (LineNotInDiff $e) {
                continue;
            }

            if ($message['type'] === 'ERROR') {
                yield Violation::newError($file->getNewName(), $position, $message['message']);
            } else {
                yield Violation::newWarning($file->getNewName(), $position, $message['message']);
            }
        }
    }
}