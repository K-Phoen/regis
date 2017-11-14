<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

use Regis\BitbucketContext\Domain\Entity;

interface ClientFactory
{
    public function createForRepository(Entity\Repository $repository): Client;

    public function createForUser(Entity\BitbucketDetails $user): Client;
}
