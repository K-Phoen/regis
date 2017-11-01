<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Regis\GithubContext\Domain\Model\PullRequest;

class PullRequestInspection extends Inspection
{
    private $pullRequestNumber;

    public static function create(Repository $repository, PullRequest $pullRequest): self
    {
        /** @var $inspection PullRequestInspection */
        $inspection = parent::createForRevisions($repository, $pullRequest->getRevisions());
        $inspection->pullRequestNumber = $pullRequest->getNumber();

        return $inspection;
    }

    public function getType(): string
    {
        return self::TYPE_GITHUB_PR;
    }

    public function getPullRequestNumber(): int
    {
        return $this->pullRequestNumber;
    }
}
