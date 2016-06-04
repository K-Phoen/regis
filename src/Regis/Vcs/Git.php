<?php

declare(strict_types=1);

namespace Regis\Vcs;

use Gitonomy\Git as Gitonomy;
use Psr\Log\LoggerInterface;

use Regis\Domain\Model;

class Git
{
    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $gitBinary;

    /** @var string */
    private $repositoriesDirectory;

    public function __construct(LoggerInterface $logger, string $gitBinary, string $repositoriesDirectory)
    {
        $this->logger = $logger;
        $this->gitBinary = $gitBinary;
        $this->repositoriesDirectory = $repositoriesDirectory;
    }

    public function getRepository(Model\Github\Repository $repository): Repository
    {
        $repositoryPath = $this->getRepositoryPath($repository);

        // TODO logs
        if (!is_dir($repositoryPath)) {
            $gitRepo = Gitonomy\Admin::cloneTo($repositoryPath, $repository->getCloneUrl(), false, [
                'command' => $this->gitBinary,
            ]);
        } else {
            $gitRepo = new Gitonomy\Repository($this->getRepositoryPath($repository), [
                'command' => $this->gitBinary,
            ]);
        }

        $gitRepo->setLogger($this->logger);

        return new Repository($gitRepo);
    }

    private function getRepositoryPath(Model\Github\Repository $repository): string
    {
        return sprintf('%s/%s/%s', $this->repositoriesDirectory, $repository->getOwner(), $repository->getName());
    }
}