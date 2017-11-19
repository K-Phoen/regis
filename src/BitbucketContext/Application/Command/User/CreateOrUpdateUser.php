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

namespace Regis\BitbucketContext\Application\Command\User;

class CreateOrUpdateUser
{
    private $username;
    private $bitbucketId;
    private $accessToken;
    private $refreshToken;
    private $accessTokenExpirationDate;

    public function __construct(string $username, string $bitbucketId, string $accessToken, string $refreshToken, \DateTimeImmutable $expirationDate)
    {
        $this->username = $username;
        $this->bitbucketId = $bitbucketId;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->accessTokenExpirationDate = $expirationDate;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getBitbucketId(): string
    {
        return $this->bitbucketId;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getAccessTokenExpirationDate(): \DateTimeImmutable
    {
        return $this->accessTokenExpirationDate;
    }
}
