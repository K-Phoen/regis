<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Vcs;

use Regis\AnalysisContext\Domain\Model\Git as Model;

interface Repository
{
    public function checkout(string $revision);

    public function getDiff(Model\Revisions $revisions): Model\Diff;

    /**
     * Locates a file in the repository.
     *
     * @note Currently only looks at the repository root.
     *
     * @param string $name The name of the file to locate.
     *
     * @return string Absolute path to the file.
     *
     * @throws FileNotFound
     */
    public function locateFile(string $name): string;
}
