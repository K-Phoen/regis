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

namespace Regis\AppContext\Application\CommandHandler\Repository;

use Regis\AppContext\Application\Command;
use Regis\GithubContext\Application\Random\Generator as RandomGenerator;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository\Repositories;
use Regis\AppContext\Domain\Repository\Exception;

class Register
{
    private $repositoriesRepo;
    private $randomGenerator;

    public function __construct(Repositories $repositoriesRepo, RandomGenerator $generator)
    {
        $this->repositoriesRepo = $repositoriesRepo;
        $this->randomGenerator = $generator;
    }

    public function handle(Command\Repository\Register $command): Entity\Repository
    {
        try {
            return $this->repositoriesRepo->findByIdentifier($command->getType(), $command->getIdentifier());
        } catch (Exception\NotFound $e) {
            // the repository does not exist yet, we can continue and create it.
        }

        $sharedSecret = $command->getSharedSecret() ?: $this->randomGenerator->randomString();
        $repository = new Entity\Repository(
            $command->getOwner(),
            $command->getType(),
            $command->getIdentifier(),
            $command->getName(),
            $sharedSecret
        );

        $this->repositoriesRepo->save($repository);

        return $repository;
    }
}
