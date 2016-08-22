<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Doctrine\ORM\QueryBuilder;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class DoctrineRepositories implements Repository\Repositories
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\Repository $team)
    {
        $this->em->persist($team);
        $this->em->flush();
    }

    /**
     * TODO handle teams
     */
    public function findForUser(Entity\User $user): \Traversable
    {
        $repo = $this->em->getRepository(Entity\Repository::class);
        /** @var QueryBuilder $qb */
        $qb = $repo->createQueryBuilder('r');

        $qb
            ->innerJoin('r.teams', 't')
            ->innerJoin('t.members', 'm')
            ->andWhere('r.owner = :user_id')
            ->orWhere('m.id = :user_id')
            ->setParameter('user_id', $user->getId());

        return new \ArrayIterator($qb->getQuery()->execute());
    }

    public function find(string $id): Entity\Repository
    {
        $repository = $this->em->getRepository(Entity\Repository::class)->find($id);

        if ($repository === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }
        
        return $repository;
    }
}
