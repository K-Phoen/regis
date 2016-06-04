<?php

namespace Regis\Vcs;

use Gitonomy\Git as Gitonomy;

use Regis\Domain\Model;

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

    public function getDiff(string $base, string $head): Gitonomy\Diff\Diff
    {
        return $this->repository->getDiff(sprintf('%s..%s', $base, $head));
    }

    public function getBlobContent(string $sha): string
    {
        return $this->repository->getBlob($sha);
    }

    public function getCommit(string $sha): Gitonomy\Commit
    {
        return $this->repository->getCommit($sha);
    }
}