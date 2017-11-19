<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Regis\Kernel;

class BitbucketDetails implements Kernel\User
{
    private $id;
    private $user;
    private $username;
    private $remoteId;
    private $accessToken;
    private $refreshToken;
    private $accessTokenExpiration;

    public function __construct(UserAccount $user, string $remoteId, string $username, string $accessToken, string $refreshToken, \DateTimeImmutable $accessTokenExpiration)
    {
        $this->id = Kernel\Uuid::create();
        $this->user = $user;
        $this->remoteId = $remoteId;
        $this->username = $username;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->accessTokenExpiration = $accessTokenExpiration;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function accountId(): string
    {
        return $this->user->accountId();
    }

    public function account(): UserAccount
    {
        return $this->user;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getAccessTokenExpiration(): \DateTimeInterface
    {
        return $this->accessTokenExpiration;
    }

    public function changeAccessToken(string $accessToken, \DateTimeImmutable $expirationDate, string $refreshToken)
    {
        if (empty($accessToken)) {
            throw new \InvalidArgumentException('The new access token can not be empty');
        }
        if (empty($refreshToken)) {
            throw new \InvalidArgumentException('The new refresh token can not be empty');
        }

        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->accessTokenExpiration = $expirationDate;
    }
}
