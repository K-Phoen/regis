<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\AnalysisContext\Domain\Model\Git\Diff;
use Regis\AnalysisContext\Domain\Model\Git\Revisions;

class PullRequestInspection extends Inspection
{
    private $pullRequestNumber;
    private $repository;

    public static function create(Repository $repository, PullRequest $pullRequest): self
    {
        /** @var $inspection PullRequestInspection */
        $inspection = parent::createForRevisions($pullRequest->getHead(), $pullRequest->getBase());
        $inspection->repository = $repository;
        $inspection->pullRequestNumber = $pullRequest->getNumber();

        return $inspection;
    }

    // TODO
    public function getDiff(): Diff
    {
        return Diff::fromRawDiff(
            new Revisions($this->getBase(), $this->getHead()),
            $this->getReport()->rawDiff()
        );
    }

    public function getType(): string
    {
        return self::TYPE_GITHUB_PR;
    }

    public function getPullRequestNumber(): int
    {
        return $this->pullRequestNumber;
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }
}
