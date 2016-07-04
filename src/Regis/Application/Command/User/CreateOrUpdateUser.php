<?php

declare(strict_types=1);

namespace Regis\Application\Command\User;

class CreateOrUpdateUser
{
    private $username;
    private $githubId;
    private $email;
    private $accessToken;

    public function __construct(string $username, int $githubId, string $email, string $accessToken)
    {
        $this->username = $username;
        $this->githubId = $githubId;
        $this->email = $email;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}