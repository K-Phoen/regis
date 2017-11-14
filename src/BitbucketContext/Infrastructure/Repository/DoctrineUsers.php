<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Repository;

use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

class DoctrineUsers implements Repository\Users
{
    use RepositoryHelper;

    public function save(Entity\BitbucketDetails $user)
    {
        $this->entityManager()->persist($user);
        $this->entityManager()->flush();
    }

    public function findByBitbucketId(string $id): Entity\BitbucketDetails
    {
        $user = $this->entityManager()->getRepository(Entity\BitbucketDetails::class)->findOneBy(['remoteId' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier((string) $id);
        }

        return $user;
    }
}
