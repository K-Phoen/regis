<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Vcs;

class FileNotFound extends \RuntimeException
{
    public static function inRepository(string $repositoryPath, string $file): self
    {
        return new static(sprintf('File "%s" not found in repository "%s"', $file, $repositoryPath));
    }
}
