<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Inspection;

use Regis\AnalysisContext\Application\Inspection;
use Regis\AnalysisContext\Application\Vcs;
use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;
use Regis\AnalysisContext\Domain\Model\Git as Model;
use Regis\AnalysisContext\Domain\Entity\Violation;

class PhpMd implements Inspection
{
    /** @var PhpMdRunner */
    private $phpMd;

    /** @var array */
    private $config;

    public function __construct(PhpMdRunner $phpMd, array $config)
    {
        $this->phpMd = $phpMd;
        $this->config = $config;
    }

    public function getType(): string
    {
        return 'phpmd';
    }

    public function inspectDiff(Vcs\Repository $repository, Model\Diff $diff): \Traversable
    {
        try {
            $ruleset = $this->locateRuleset($repository);
        } catch (Exception\ConfigurationNotFound $e) {
            $ruleset = implode(',', $this->config['rulesets']);
        }

        /** @var Model\Diff\File $file */
        foreach ($diff->getAddedPhpFiles() as $file) {
            $report = $this->phpMd->execute($file->getNewName(), $file->getNewContent(), $ruleset);

            yield from $this->buildViolations($file, $report);
        }
    }

    private function locateRuleset(Vcs\Repository $repository): string
    {
        try {
            return $repository->locateFile('phpmd-ruleset.xml');
        } catch (Vcs\FileNotFound $e) {
            throw new Exception\ConfigurationNotFound($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function buildViolations(Model\Diff\File $file, \Traversable $report): \Traversable
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
