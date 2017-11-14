<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Regis\Kernel;

class GithubDetails implements Kernel\User
{
    private $id;
    private $user;
    private $remoteId;
    private $accessToken;

    public function __construct(Kernel\User $user, int $remoteId, string $accessToken)
    {
        $this->id = Kernel\Uuid::create();
        $this->user = $user;
        $this->remoteId = $remoteId;
        $this->accessToken = $accessToken;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRemoteId(): int
    {
        return $this->remoteId;
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

    public function changeAccessToken(string $accessToken)
    {
        if (empty($accessToken)) {
            throw new \InvalidArgumentException('The new access token can not be empty');
        }

        $this->accessToken = $accessToken;
    }
}
