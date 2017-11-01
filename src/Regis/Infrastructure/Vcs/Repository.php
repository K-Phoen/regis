<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Vcs;

use Gitonomy\Git as Gitonomy;

use Regis\Application\Vcs\FileNotFound;
use Regis\Domain\Model\Git as Model;
use Symfony\Component\Filesystem\Filesystem;

class Repository implements \Regis\Application\Vcs\Repository
{
    /** @var Gitonomy\Repository */
    private $repository;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(Gitonomy\Repository $repository, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
    }

    public function checkout(string $revision)
    {
        $this->repository->run('fetch');
        $this->repository->getWorkingCopy()->checkout($revision);
    }

    public function locateFile(string $name): string
    {
        $repositoryPath = $this->repository->getPath();

        if (!$this->filesystem->exists($name)) {
            throw FileNotFound::inRepository($repositoryPath, $name);
        }

        return $repositoryPath.'/'.$name;
    }

    public function getDiff(Model\Revisions $revisions): Model\Diff
    {
        $gitDiff = $this->repository->getDiff(sprintf('%s..%s', $revisions->getBase(), $revisions->getHead()));

        $diff = Model\Diff::fromRawDiff($revisions, $gitDiff->getRawDiff());

        return $this->augmentWithFileContents($diff);
    }

    private function augmentWithFileContents(Model\Diff $diff): Model\Diff
    {
        $files = array_map(function (Model\Diff\File $file) {
            $blob = $this->repository->getBlob($file->getNewIndex());

            return $file->replaceNewContent(
                new Model\Blob($blob->getHash(), $blob->getContent(), $blob->getMimetype())
            );
        }, $diff->getFiles());

        return new Model\Diff($diff->getRevisions(), $files, $diff->getRawDiff());
    }
}
