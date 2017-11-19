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

namespace Regis\GithubContext\Application\Command\Repository;

use Regis\GithubContext\Domain\Model\RepositoryIdentifier;

class CreateWebhook
{
    private $repository;
    private $callbackUrl;

    /**
     * @param string $callbackUrl absolute URL
     */
    public function __construct(RepositoryIdentifier $repository, string $callbackUrl)
    {
        $this->repository = $repository;
        $this->callbackUrl = $callbackUrl;
    }

    public function getRepository(): RepositoryIdentifier
    {
        return $this->repository;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }
}
