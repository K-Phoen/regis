<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Infrastructure\Github;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\BitbucketClient as OauthBitbucketClient;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Infrastructure\Bitbucket\Client;
use Regis\BitbucketContext\Infrastructure\Bitbucket\ClientFactory;
use Regis\BitbucketContext\Infrastructure\Bitbucket\RefreshTokenAwareClientFactory;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Application\Command;

class RefreshTokenAwareClientFactoryTest extends TestCase
{
    private $bus;
    private $decoratedFactory;
    private $oauthClient;

    /** @var ClientFactory */
    private $clientFactory;

    public function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->bus = $this->createMock(CommandBus::class);
        $this->decoratedFactory = $this->createMock(BitbucketClientFactory::class);
        $this->oauthClient = $this->createMock(OauthBitbucketClient::class);

        $this->clientFactory = new RefreshTokenAwareClientFactory(
            $this->bus,
            $this->decoratedFactory,
            $this->oauthClient,
            $logger
        );
    }

    public function testItJustDelegatesIfTheAccessTokenIsValid()
    {
        $client = $this->createMock(Client::class);
        $user = $this->createMock(BitbucketDetails::class);
        $user->method('isAccessTokenObsolete')->with($this->isInstanceOf(\DateTimeImmutable::class))->willReturn(false);

        $this->bus->expects($this->never())->method('handle');

        $this->decoratedFactory->expects($this->once())
            ->method('createForUser')
            ->with($user)
            ->willReturn($client);

        $this->assertSame($client, $this->clientFactory->createForUser($user));
    }

    public function testItRefreshesTheTokenIfTheAccessTokenIsValid()
    {
        $oauthProvider = $this->createMock(AbstractProvider::class);
        $accessToken = $this->createMock(AccessToken::class);
        $client = $this->createMock(Client::class);
        $user = $this->createMock(BitbucketDetails::class);

        $user->method('isAccessTokenObsolete')->with($this->isInstanceOf(\DateTimeImmutable::class))->willReturn(true);
        $user->method('getAccessTokenExpiration')->willReturn(new \DateTimeImmutable());
        $user->method('getRefreshToken')->willReturn('old refresh token');

        $accessToken->method('getToken')->willReturn('new access token');
        $accessToken->method('getRefreshToken')->willReturn('new refresh token');
        $accessToken->method('getExpires')->willReturn($newExpire = 1511103387);

        $this->oauthClient->method('getOAuth2Provider')->willReturn($oauthProvider);

        $oauthProvider
            ->method('getAccessToken')
            ->with('refresh_token', [
                'refresh_token' => 'old refresh token',
            ])
            ->willReturn($accessToken);

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (Command\User\CreateOrUpdateUser $command) use ($newExpire) {
                $this->assertSame('new access token', $command->getAccessToken());
                $this->assertSame('new refresh token', $command->getRefreshToken());
                $this->assertSame($newExpire, $command->getAccessTokenExpirationDate()->getTimestamp());

                return true;
            }));

        $this->decoratedFactory->expects($this->once())
            ->method('createForUser')
            ->with($user)
            ->willReturn($client);

        $this->assertSame($client, $this->clientFactory->createForUser($user));
    }
}
