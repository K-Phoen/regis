<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Repository;

use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

class DoctrineUsers implements Repository\Users
{
    use RepositoryHelper;

    public function save(Entity\BitbucketDetails $user): void
    {
        $this->entityManager()->persist($user);
        $this->entityManager()->flush();
    }

    public function findByBitbucketId(string $id): Entity\BitbucketDetails
    {
        $user = $this->entityManager()->getRepository(Entity\BitbucketDetails::class)->findOneBy(['remoteId' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }

    public function findByAccountId(string $id): Entity\BitbucketDetails
    {
        $user = $this->entityManager()->getRepository(Entity\BitbucketDetails::class)->findOneBy(['user' => $id]);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }
}
