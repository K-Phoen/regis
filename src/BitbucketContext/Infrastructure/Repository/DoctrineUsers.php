<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Repository;

use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

class DoctrineUsers implements Repository\Users
{
    use RepositoryHelper;

    public function save(Entity\User $user)
    {
        $this->entityManager()->persist($user);
        $this->entityManager()->flush();
    }

    public function findByBitbucketId(string $id): Entity\User
    {
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $this->entityManager()->getRepository(Entity\User::class);
        $qb = $repo->createQueryBuilder('u');

        $qb
            ->innerJoin('u.details', 'details')
            ->andWhere('details.remoteId = :bitbucketId')
            ->setParameters([
                'bitbucketId' => $id,
            ])
        ;
        $user = $qb->getQuery()->getOneOrNullResult();

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier((string) $id);
        }

        return $user;
    }

    public function findById(string $id): Entity\User
    {
        $user = $this->em->getRepository(Entity\User::class)->find($id);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }
}
