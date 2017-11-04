<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class DoctrinePullRequestInspections implements Repository\PullRequestInspections
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\PullRequestInspection $inspections)
    {
        $this->em->persist($inspections);
        $this->em->flush();
    }

    public function find(string $id): Entity\PullRequestInspection
    {
        $inspection = $this->em->getRepository(Entity\PullRequestInspection::class)->find($id);

        if ($inspection === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $inspection;
    }
}
