<?php

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
