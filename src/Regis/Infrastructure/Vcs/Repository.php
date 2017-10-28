<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Vcs;

use Gitonomy\Git as Gitonomy;

use Regis\Domain\Model\Git as Model;

class Repository
{
    /** @var Gitonomy\Repository */
    private $repository;

    public function __construct(Gitonomy\Repository $repository)
    {
        $this->repository = $repository;
    }

    public function update()
    {
        $this->repository->run('fetch');
    }

    public function getDiff(Model\Revisions $revisions): Model\Diff
    {
        $gitDiff = $this->repository->getDiff(sprintf('%s..%s', $revisions->getBase(), $revisions->getHead()));

        $diff = Model\Diff::fromRawDiff($revisions, $gitDiff->getRawDiff());

        return $this->augmentWithFileContents($diff);
    }

    private function augmentWithFileContents(Model\Diff $diff): Model\Diff
    {
        $files = array_map(function (Model\Diff\File $file) use ($diff) {
            $blob = $this->repository->getBlob($file->getNewIndex());

            return $file->replaceNewContent(
                new Model\Blob($blob->getHash(), $blob->getContent(), $blob->getMimetype())
            );
        }, $diff->getFiles());

        return new Model\Diff($diff->getRevisions(), $files, $diff->getRawDiff());
    }
}
