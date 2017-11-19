<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Command\Inspection;

use Regis\BitbucketContext\Domain\Entity;

class SendViolationsAsComments
{
    private $inspection;

    public function __construct(Entity\PullRequestInspection $inspection)
    {
        $this->inspection = $inspection;
    }

    public function getInspection(): Entity\PullRequestInspection
    {
        return $this->inspection;
    }
}
