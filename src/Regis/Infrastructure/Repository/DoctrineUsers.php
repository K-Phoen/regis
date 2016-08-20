<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

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
        $repo = $this->em->getRepository(Entity\User::class);
        $qb = $repo->createQueryBuilder('u');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function findByGithubId(int $id): Entity\User
    {
        $user = $this->em->getRepository(Entity\User::class)->findOneBy(['githubId' => $id]);

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

    public function findByUsername(string $username): Entity\User
    {
        $user = $this->em->getRepository(Entity\User::class)->findOneBy(['username' => $username]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($username);
        }

        return $user;
    }
}
