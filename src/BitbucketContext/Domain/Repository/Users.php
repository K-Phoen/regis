<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Repository;

use Regis\BitbucketContext\Domain\Entity;

interface Users
{
    public function save(Entity\BitbucketDetails $user);

    public function findByBitbucketId(string $id): Entity\BitbucketDetails;
    public function findByAccountId(string $id): Entity\BitbucketDetails;
}
