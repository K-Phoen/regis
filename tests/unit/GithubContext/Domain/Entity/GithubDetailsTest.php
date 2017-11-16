<?php

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\GithubDetails;
use Regis\GithubContext\Domain\Entity\UserAccount;

class GithubDetailsTest extends TestCase
{
    public function testItGeneratesAnIdentifierAndStoreTheInitialData()
    {
        $userAccount = new UserAccount();
        $githubDetails = new GithubDetails($userAccount, 42, 'access token');

        $this->assertNotEmpty($githubDetails->getId());
        $this->assertSame($userAccount, $githubDetails->account());
        $this->assertSame(42, $githubDetails->getRemoteId());
        $this->assertSame('access token', $githubDetails->getAccessToken());
        $this->assertSame($userAccount->accountId(), $githubDetails->accountId());
    }

    public function testTheAccessTokenCanBeChanged()
    {
        $githubDetails = new GithubDetails(new UserAccount(), 42, 'access token');

        $this->assertSame('access token', $githubDetails->getAccessToken());

        $githubDetails->changeAccessToken('new access token');

        $this->assertSame('new access token', $githubDetails->getAccessToken());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new access token can not be empty
     */
    public function testTheAccessTokenCanNotBeEmpty()
    {
        $githubDetails = new GithubDetails(new UserAccount(), 42, 'access token');

        $githubDetails->changeAccessToken('');
    }
}
