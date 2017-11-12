<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Inspection;

use Regis\GithubContext\Domain\Entity;

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
