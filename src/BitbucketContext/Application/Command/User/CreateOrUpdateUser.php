<?php

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
