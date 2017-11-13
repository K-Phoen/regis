<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Command\User;

class CreateOrUpdateUser
{
    private $username;
    private $bitbucketId;
    private $accessToken;

    public function __construct(string $username, string $bitbucketId, string $accessToken)
    {
        $this->username = $username;
        $this->bitbucketId = $bitbucketId;
        $this->accessToken = $accessToken;
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
}
