<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Regis\BitbucketContext\Domain\Model;

class PullRequestInspection extends Inspection
{
    private $pullRequestNumber;

    public static function create(Repository $repository, Model\PullRequest $pullRequest): self
    {
        /** @var $inspection PullRequestInspection */
        $inspection = parent::createForRevisions($repository, $pullRequest->getHead(), $pullRequest->getBase());
        $inspection->pullRequestNumber = $pullRequest->getNumber();

        return $inspection;
    }

    public function getPullRequestNumber(): int
    {
        return $this->pullRequestNumber;
    }

    public function getPullRequest(): Model\PullRequest
    {
        return new Model\PullRequest(
            $this->getRepository()->toIdentifier(),
            $this->pullRequestNumber,
            $this->getHead(),
            $this->getBase()
        );
    }
}
