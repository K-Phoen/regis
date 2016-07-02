<?php

declare(strict_types=1);

namespace Regis\Infrastructure\PhpMd;

use Symfony\Component\Process\Process;
use Regis\Application\Inspection\PhpMdRunner;

class PhpMd implements PhpMdRunner
{
    private $phpmdBin;
    private $config;
    private $tempDir;

    public function __construct(string $phpCsBin, array $config = [], string $tempDir = null)
    {
        $this->phpmdBin = $phpCsBin;
        $this->config = $config;
        $this->tempDir = $tempDir ?: sys_get_temp_dir();
    }

    public function execute(string $fileName, string $fileContent): \Traversable
    {
        $tempFile = sprintf('%s/%s', $this->tempDir, uniqid('phpmd_', true).str_replace('/', '', $fileName));

        file_put_contents($tempFile, $fileContent);

        try {
            $process = new Process(sprintf(
                '%s %s xml %s',
                escapeshellarg($this->phpmdBin),
                escapeshellarg($tempFile),
                escapeshellarg(implode(',', $this->config['rulesets']))
            ));

            $process->run();
        } finally {
            unlink($tempFile);
        }

        yield from $this->processResults($fileName, $process->getOutput());
    }

    private function processResults(string $originalFileName, string $xmlReport): \Traversable
    {
        $xml = new \SimpleXMLElement($xmlReport);

        /** @var \SimpleXMLElement $file */
        foreach ($xml->file as $file) {
            /** @var \SimpleXMLElement $violation */
            foreach ($file->violation as $violation) {
                yield [
                    'file' => $originalFileName,
                    'beginLine' => (int) (string) $violation['beginline'],
                    'endLine' => (int) (string) $violation['endline'],
                    'rule' => (string) $violation['rule'],
                    'ruleSet' => (string) $violation['ruleset'],
                    'externalInfoUrl' => (string) $violation['externalInfoUrl'],
                    'priority' => (int) (string) $violation['priority'],
                    'description' => trim((string) $violation),
                ];
            }
        }
    }
}