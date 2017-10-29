<?php

declare(strict_types=1);

namespace Regis\Application\Command\Git;

use Regis\Domain\Model\Git;

class InspectRevisions
{
    private $repository;
    private $revisions;

    public function __construct(Git\Repository $repository, Git\Revisions $revisions)
    {
        $this->repository = $repository;
        $this->revisions = $revisions;
    }

    public function getRepository(): Git\Repository
    {
        return $this->repository;
    }

    public function getRevisions(): Git\Revisions
    {
        return $this->revisions;
    }
}