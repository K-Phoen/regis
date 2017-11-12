<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Command;

use Regis\AnalysisContext\Domain\Model\Git;

class InspectRevisions
{
    private $inspectionId;
    private $repository;
    private $revisions;

    public function __construct(string $inspectionId, Git\Repository $repository, Git\Revisions $revisions)
    {
        $this->inspectionId = $inspectionId;
        $this->repository = $repository;
        $this->revisions = $revisions;
    }

    public function getInspectionId(): string
    {
        return $this->inspectionId;
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
