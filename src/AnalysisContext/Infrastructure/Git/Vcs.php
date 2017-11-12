<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Infrastructure\Git;

use Gitonomy\Git as Gitonomy;
use Psr\Log\LoggerInterface as Logger;

use Regis\AnalysisContext\Application\Vcs\Git;
use Regis\AnalysisContext\Domain\Model;
use Symfony\Component\Filesystem\Filesystem;

class Vcs implements Git
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Logger */
    private $logger;

    /** @var string */
    private $repositoriesDirectory;

    /** @var string */
    private $gitBinary;

    private $gitonomyOptions = [];

    public function __construct(Filesystem $filesystem, Logger $logger, string $gitBinary, string $repositoriesDirectory)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->gitBinary = $gitBinary;
        $this->repositoriesDirectory = $repositoriesDirectory;

        $this->gitonomyOptions = [
            'command' => $gitBinary,
        ];
    }

    public function getRepository(Model\Git\Repository $repository): \Regis\AnalysisContext\Application\Vcs\Repository
    {
        $repositoryPath = $this->getRepositoryPath($repository);

        if (!is_dir($repositoryPath)) {
            $gitRepo = Gitonomy\Admin::cloneTo($repositoryPath, $repository->getCloneUrl(), false, $this->gitonomyOptions);
        } else {
            $gitRepo = new Gitonomy\Repository($this->getRepositoryPath($repository), $this->gitonomyOptions);
        }

        $gitRepo->setLogger($this->logger);

        return new Repository($gitRepo, $this->filesystem);
    }

    private function getRepositoryPath(Model\Git\Repository $repository): string
    {
        return sprintf('%s/%s/%s', $this->repositoriesDirectory, $repository->getOwner(), $repository->getName());
    }
}
