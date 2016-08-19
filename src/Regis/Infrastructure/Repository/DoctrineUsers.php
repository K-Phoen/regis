<?php

declare(strict_types=1);

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

    /**
     * TODO improve!
     */
    public function search(string $terms): \Traversable
    {
        $repo = $this->em->getRepository(Entity\User::class);

        return new \ArrayIterator($repo->createQueryBuilder('u')
            ->where('u.username LIKE :terms OR u.email LIKE :terms')
            ->setParameter('terms', '%'.$terms.'%')
            ->getQuery()
            ->getResult());
    }

    public function findByGithubId(int $id): Entity\User
    {
        $user = $this->em->getRepository(Entity\User::class)->findOneBy(['githubId' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
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
