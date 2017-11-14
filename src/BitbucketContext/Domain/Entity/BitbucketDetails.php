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

    public function __construct(Kernel\User $user, string $remoteId, string $username, string $accessToken)
    {
        $this->id = Kernel\Uuid::create();
        $this->user = new UserAccount();
        $this->remoteId = $remoteId;
        $this->username = $username;
        $this->accessToken = $accessToken;
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
