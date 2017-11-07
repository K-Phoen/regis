<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class DoctrineUsers implements Repository\Users
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

    public function save(Entity\User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function matching(Specification $spec): \Traversable
    {
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $this->em->getRepository(Entity\User::class);
        $qb = $repo->createQueryBuilder('u');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function findByGithubId(int $id): Entity\User
    {
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $this->em->getRepository(Entity\User::class);
        $qb = $repo->createQueryBuilder('u');

        $qb
            ->innerJoin('u.details', 'details')
            ->andWhere('details.remoteId = :githubId')
            ->setParameters([
                'githubId' => $id,
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
