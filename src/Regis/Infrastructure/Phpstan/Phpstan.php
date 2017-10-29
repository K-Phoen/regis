<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Phpstan;

use Regis\Application\Inspection\PhpstanRunner;
use Symfony\Component\Process\Process;

class Phpstan implements PhpstanRunner
{
    private $phpstanBin;

    public function __construct(string $phpCsBin)
    {
        $this->phpstanBin = $phpCsBin;
    }

    public function execute(string $fileName): \Traversable
    {
        $process = new Process(sprintf(
            '%s analyse --no-progress --level=7 --errorFormat=checkstyle %s',
            escapeshellarg($this->phpstanBin),
            escapeshellarg($fileName)
        ));
        $process->run();

        yield from $this->processResults($fileName, $process->getOutput());
    }

    private function processResults(string $originalFileName, string $xmlReport): \Traversable
    {
        $xml = new \SimpleXMLElement($xmlReport);

        /** @var \SimpleXMLElement $file */
        foreach ($xml->file as $file) {
            /** @var \SimpleXMLElement $violation */
            foreach ($file->error as $violation) {
                yield [
                    'file' => $originalFileName,
                    'line' => (int) (string) $violation['line'],
                    'column' => (int) (string) $violation['column'],
                    'severity' => (string) $violation['severity'],
                    'message' => (string) $violation['message'],
                ];
            }
        }
    }
}
