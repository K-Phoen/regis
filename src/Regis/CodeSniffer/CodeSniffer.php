<?php

declare(strict_types=1);

namespace Regis\CodeSniffer;

use Symfony\Component\Process\Process;

class CodeSniffer
{
    private $phpcsBin;
    private $codeSnifferConfig;

    public function __construct(string $phpCsBin, array $codeSnifferConfig = [])
    {
        $this->phpcsBin = $phpCsBin;
        $this->codeSnifferConfig = $codeSnifferConfig;
    }

    public function execute(string $fileName, string $fileContent): array
    {
        $process = new Process(sprintf(
            '%s %s --report=json --stdin-path=%s',
            escapeshellarg($this->phpcsBin),
            implode(' ', $this->codeSnifferConfig['options']),
            escapeshellarg($fileName)
        ));

        $process->setInput($fileContent);
        $process->run();

        return json_decode($process->getOutput(), true);
    }
}