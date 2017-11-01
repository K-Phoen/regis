<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\User;

class CreateOrUpdateUser
{
    private $username;
    private $githubId;
    private $email;
    private $accessToken;

    public function __construct(string $username, int $githubId, string $accessToken, string $email = null)
    {
        $this->username = $username;
        $this->githubId = $githubId;
        $this->accessToken = $accessToken;
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getGithubId(): int
    {
        return $this->githubId;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
