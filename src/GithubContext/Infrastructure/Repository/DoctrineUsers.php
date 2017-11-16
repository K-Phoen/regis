<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class DoctrineUsers implements Repository\Users
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\GithubDetails $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function findByGithubId(int $id): Entity\GithubDetails
    {
        $user = $this->em->getRepository(Entity\GithubDetails::class)->findOneBy(['remoteId' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier((string) $id);
        }

        return $user;
    }

    public function findByAccountId(string $id): Entity\GithubDetails
    {
        $user = $this->em->getRepository(Entity\GithubDetails::class)->findOneBy(['user' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier((string) $id);
        }

        return $user;
    }
}
