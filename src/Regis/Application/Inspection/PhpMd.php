<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

use Regis\Application\Inspection;
use Regis\Domain\Model\Exception\LineNotInDiff;
use Regis\Domain\Model\Git as Model;
use Regis\Domain\Entity\Inspection\Violation;
use Regis\PhpMd\PhpMd as PhpMdRunner;

class PhpMd implements Inspection
{
    private $phpMd;

    public function __construct(PhpMdRunner $phpMd)
    {
        $this->phpMd = $phpMd;
    }

    public function getType(): string
    {
        return 'phpmd';
    }

    public function inspectDiff(Model\Diff $diff): \Traversable
    {
        /** @var Model\Diff\File $file */
        foreach ($diff->getAddedTextFiles() as $file) {
            $report = $this->phpMd->execute($file->getNewName(), $file->getNewContent());

            yield from $this->buildViolations($file, $report);
        }
    }

    public function buildViolations(Model\Diff\File $file, \Traversable $report): \Traversable
    {
        foreach ($report as $violation) {
            try {
                yield $this->buildViolation($file, $violation);
            } catch (LineNotInDiff $e) {
                continue;
            }
        }
    }

    private function buildViolation(Model\Diff\File $file, array $report): Violation
    {
        $position = $file->findPositionForLine($report['beginLine']);

        if (in_array($report['priority'], [1, 2], true)) {
            return Violation::newError($file->getNewName(), $report['beginLine'], $position, $report['description']);
        }

        return Violation::newWarning($file->getNewName(), $report['beginLine'], $position, $report['description']);
    }
}