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

namespace Regis\GithubContext\Domain\Entity;

use Regis\Kernel;

class UserAccount implements Kernel\User
{
    private $id;
    private $repositories;
    private $details;

    public function __construct()
    {
        $this->id = Kernel\Uuid::create();
    }

    public function accountId(): string
    {
        return $this->id;
    }

    public function getDetails(): GithubDetails
    {
        return $this->details;
    }

    public function getRepositories(): \Traversable
    {
        return $this->repositories;
    }
}
