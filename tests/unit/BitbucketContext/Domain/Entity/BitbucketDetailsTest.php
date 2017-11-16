<?php

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Entity\UserAccount;

class BitbucketDetailsTest extends TestCase
{
    public function testItGeneratesAnIdentifierAndStoreTheInitialData()
    {
        $userAccount = new UserAccount();
        $details = new BitbucketDetails($userAccount, 'remote-id', 'username', 'access token');

        $this->assertNotEmpty($details->getId());
        $this->assertSame($userAccount, $details->account());
        $this->assertSame('remote-id', $details->getRemoteId());
        $this->assertSame('username', $details->getUsername());
        $this->assertSame('access token', $details->getAccessToken());
        $this->assertSame($userAccount->accountId(), $details->accountId());
    }

    public function testTheAccessTokenCanBeChanged()
    {
        $details = new BitbucketDetails(new UserAccount(), 'remote-id', 'username', 'access token');

        $this->assertSame('access token', $details->getAccessToken());

        $details->changeAccessToken('new access token');

        $this->assertSame('new access token', $details->getAccessToken());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new access token can not be empty
     */
    public function testTheAccessTokenCanNotBeEmpty()
    {
        $details = new BitbucketDetails(new UserAccount(), 'remote-id', 'username', 'access token');

        $details->changeAccessToken('');
    }
}
