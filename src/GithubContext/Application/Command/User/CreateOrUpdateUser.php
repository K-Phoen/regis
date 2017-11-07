<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\User;

class CreateOrUpdateUser
{
    private $username;
    private $githubId;
    private $accessToken;

    public function __construct(string $username, int $githubId, string $accessToken)
    {
        $this->username = $username;
        $this->githubId = $githubId;
        $this->accessToken = $accessToken;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getGithubId(): int
    {
        return $this->githubId;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
