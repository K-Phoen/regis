<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Repository;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

class DoctrineTeams implements Repository\Teams
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RulerZ */
    private $rulerz;

    public function __construct(EntityManagerInterface $em, RulerZ $rulerz)
    {
        $this->em = $em;
        $this->rulerz = $rulerz;
    }

    public function save(Entity\Team $team)
    {
        $this->em->persist($team);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new Repository\Exception\UniqueConstraintViolation($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function matching(Specification $spec): \Traversable
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repo()->createQueryBuilder('t');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function find(string $id): Entity\Team
    {
        $team = $this->repo()->find($id);

        if ($team === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }
        
        return $team;
    }

    private function repo()
    {
        return $this->em->getRepository(Entity\Team::class);
    }
}
