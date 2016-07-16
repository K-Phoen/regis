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

        return Model\Diff::fromRawDiff($revisions, $gitDiff->getRawDiff());
    }
}
