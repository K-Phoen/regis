<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Regis\Kernel\Uuid;

class BitbucketDetails
{
    private $id;
    private $user;
    private $remoteId;
    private $accessToken;

    public function __construct(User $user, int $remoteId, string $accessToken)
    {
        $this->id = Uuid::create();
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
