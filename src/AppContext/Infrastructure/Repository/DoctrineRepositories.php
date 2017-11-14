<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

class DoctrineRepositories implements Repository\Repositories
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

    public function save(Entity\Repository $team)
    {
        $this->em->persist($team);
        $this->em->flush();
    }

    public function matching(Specification $spec): \Traversable
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repo()->createQueryBuilder('r');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function find(string $id, $mode = self::MODE_FETCH_NOTHING): Entity\Repository
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repo()->createQueryBuilder('r');

        $qb
            ->where('r.identifier = :identifier')
            ->setParameter('identifier', $id);

        if ($mode === self::MODE_FETCH_RELATIONS) {
            $qb
                ->addSelect(['i', 'report', 'a', 'v'])
                ->leftJoin('r.inspections', 'i')
                ->leftJoin('i.report', 'report')
                ->leftJoin('report.analyses', 'a')
                ->leftJoin('a.violations', 'v')
                ->orderBy('i.createdAt', 'DESC');
        }

        $repository = $qb->getQuery()->getOneOrNullResult();

        if ($repository === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $repository;
    }

    private function repo(): EntityRepository
    {
        return $this->em->getRepository(Entity\Repository::class);
    }
}
