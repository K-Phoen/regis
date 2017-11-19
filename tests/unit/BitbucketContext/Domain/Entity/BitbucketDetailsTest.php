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

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Entity\UserAccount;

class BitbucketDetailsTest extends TestCase
{
    public function testItGeneratesAnIdentifierAndStoreTheInitialData()
    {
        $userAccount = new UserAccount();
        $accessTokenExpiration = new \DateTimeImmutable();
        $details = new BitbucketDetails($userAccount, 'remote-id', 'username', 'access token', 'refresh token', $accessTokenExpiration);

        $this->assertNotEmpty($details->getId());
        $this->assertSame($userAccount, $details->account());
        $this->assertSame('remote-id', $details->getRemoteId());
        $this->assertSame('username', $details->getUsername());
        $this->assertSame('access token', $details->getAccessToken());
        $this->assertSame('refresh token', $details->getRefreshToken());
        $this->assertSame($accessTokenExpiration, $details->getAccessTokenExpiration());
        $this->assertSame($userAccount->accountId(), $details->accountId());
    }

    public function testTheAccessTokenCanBeChanged()
    {
        $accessTokenExpiration = new \DateTimeImmutable();
        $newAccessTokenExpiration = new \DateTimeImmutable();
        $details = new BitbucketDetails(new UserAccount(), 'remote-id', 'username', 'access token', 'refresh token', $accessTokenExpiration);

        $this->assertSame('access token', $details->getAccessToken());
        $this->assertSame('refresh token', $details->getRefreshToken());
        $this->assertSame($accessTokenExpiration, $details->getAccessTokenExpiration());

        $details->changeAccessToken('new access token', $newAccessTokenExpiration, 'new refresh token');

        $this->assertSame('new access token', $details->getAccessToken());
        $this->assertSame('new refresh token', $details->getRefreshToken());
        $this->assertSame($newAccessTokenExpiration, $details->getAccessTokenExpiration());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new access token can not be empty
     */
    public function testTheAccessTokenCanNotBeEmpty()
    {
        $accessTokenExpiration = new \DateTimeImmutable();
        $details = new BitbucketDetails(new UserAccount(), 'remote-id', 'username', 'access token', 'refresh token', $accessTokenExpiration);

        $details->changeAccessToken('', new \DateTimeImmutable(), 'new refresh token');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new refresh token can not be empty
     */
    public function testTheRefreshTokenCanNotBeEmpty()
    {
        $accessTokenExpiration = new \DateTimeImmutable();
        $details = new BitbucketDetails(new UserAccount(), 'remote-id', 'username', 'access token', 'refresh token', $accessTokenExpiration);

        $details->changeAccessToken('access token', new \DateTimeImmutable(), '');
    }
}
