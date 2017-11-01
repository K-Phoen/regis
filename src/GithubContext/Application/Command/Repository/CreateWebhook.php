<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Repository;

class CreateWebhook
{
    private $owner;
    private $repo;
    private $callbackUrl;

    /**
     * @param string $owner Owner of the repository.
     * @param string $repo Name of the repository.
     * @param string $callbackUrl Absolute URL.
     */
    public function __construct(string $owner, string $repo, string $callbackUrl)
    {
        $this->owner = $owner;
        $this->repo = $repo;
        $this->callbackUrl = $callbackUrl;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getRepo(): string
    {
        return $this->repo;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }
}
