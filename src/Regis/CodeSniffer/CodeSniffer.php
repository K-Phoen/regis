<?php

declare(strict_types=1);

namespace Regis\CodeSniffer;

use Symfony\Component\Process\Process;

class CodeSniffer
{
    private $phpcsBin;

    public function __construct(string $phpCsBin)
    {
        $this->phpcsBin = $phpCsBin;
    }

    public function execute(string $fileName, string $fileContent): array
    {
        $process = new Process(sprintf('%s --report=json --stdin-path=%s', escapeshellarg($this->phpcsBin), escapeshellarg($fileName)));
        $process->setInput($fileContent);
        $process->run();

        return json_decode($process->getOutput(), true);
    }
}