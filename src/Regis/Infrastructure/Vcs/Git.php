<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Vcs;

use Gitonomy\Git as Gitonomy;
use Psr\Log\LoggerInterface as Logger;

use Regis\Domain\Model;

class Git
{
    /** @var Logger */
    private $logger;

    /** @var string */
    private $repositoriesDirectory;

    private $gitonomyOptions = [];

    public function __construct(Logger $logger, string $gitBinary, string $repositoriesDirectory)
    {
        $this->logger = $logger;
        $this->gitBinary = $gitBinary;
        $this->repositoriesDirectory = $repositoriesDirectory;

        $this->gitonomyOptions = [
            'command' => $gitBinary,
        ];
    }

    public function getRepository(Model\Git\Repository $repository): Repository
    {
        $repositoryPath = $this->getRepositoryPath($repository);

        if (!is_dir($repositoryPath)) {
            $gitRepo = Gitonomy\Admin::cloneTo($repositoryPath, $repository->getCloneUrl(), false, $this->gitonomyOptions);
        } else {
            $gitRepo = new Gitonomy\Repository($this->getRepositoryPath($repository), $this->gitonomyOptions);
        }

        $gitRepo->setLogger($this->logger);

        return new Repository($gitRepo);
    }

    private function getRepositoryPath(Model\Git\Repository $repository): string
    {
        return sprintf('%s/%s/%s', $this->repositoriesDirectory, $repository->getOwner(), $repository->getName());
    }
}