<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository;

use Regis\GithubContext\Domain\Entity;

interface Users
{
    public function save(Entity\GithubDetails $user);

    public function findByGithubId(int $id): Entity\GithubDetails;

    public function findByAccountId(string $id): Entity\GithubDetails;
}
