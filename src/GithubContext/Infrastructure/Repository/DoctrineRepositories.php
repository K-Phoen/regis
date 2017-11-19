<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

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

    public function find(string $id): Entity\Repository
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repo()->createQueryBuilder('r');

        $qb
            ->where('r.identifier = :identifier')
            ->setParameter('identifier', $id);

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
