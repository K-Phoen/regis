<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Repository;

use Regis\GithubContext\Domain\Model\RepositoryIdentifier;

class CreateWebhook
{
    private $repository;
    private $callbackUrl;

    /**
     * @param string $callbackUrl absolute URL
     */
    public function __construct(RepositoryIdentifier $repository, string $callbackUrl)
    {
        $this->repository = $repository;
        $this->callbackUrl = $callbackUrl;
    }

    public function getRepository(): RepositoryIdentifier
    {
        return $this->repository;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }
}
