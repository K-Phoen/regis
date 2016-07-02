<?php

declare(strict_types=1);

namespace Regis\Domain\Entity\Github;

use Regis\Domain\Entity\Inspection;
use Regis\Domain\Model\Github\PullRequest;

class PullRequestInspection extends Inspection
{
    private $pullRequestNumber;

    public static function create(Repository $repository, PullRequest $pullRequest)
    {
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
