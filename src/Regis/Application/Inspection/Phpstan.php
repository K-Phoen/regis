<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

use Regis\Application\Inspection;
use Regis\Application\Vcs;
use Regis\Domain\Model\Exception\LineNotInDiff;
use Regis\Domain\Model\Git as Model;
use Regis\Domain\Entity\Inspection\Violation;

class Phpstan implements Inspection
{
    private $phpstan;

    public function __construct(PhpstanRunner $phpstan)
    {
        $this->phpstan = $phpstan;
    }

    public function getType(): string
    {
        return 'phpstan';
    }

    public function inspectDiff(Vcs\Repository $repository, Model\Diff $diff): \Traversable
    {
        /** @var Model\Diff\File $file */
        foreach ($diff->getAddedPhpFiles() as $file) {
            $report = $this->phpstan->execute($file->getNewName());

            foreach ($report as $entry) {
                try {
                    yield $this->buildViolation($file, $entry);
                } catch (LineNotInDiff $e) {
                    continue;
                }
            }
        }
    }

    private function buildViolation(Model\Diff\File $file, array $report): Violation
    {
        $position = $file->findPositionForLine($report['line']);

        return Violation::newError($file->getNewName(), $report['line'], $position, $report['message']);
    }
}
