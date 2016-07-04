<?php

namespace Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

class DoctrineUsers implements Repository\Users
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function findByGithubId(int $id): Entity\User
    {
        $user = $this->em->getRepository(Entity\User::class)->findOneBy(['githubId' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }

    public function findById(int $id): Entity\User
    {
        $user = $this->em->getRepository(Entity\User::class)->find($id);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }
}
