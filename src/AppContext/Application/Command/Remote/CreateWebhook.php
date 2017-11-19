<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command\Remote;

use Regis\AppContext\Domain\Entity;

class CreateWebhook
{
    private $repository;
    private $url;

    public function __construct(Entity\Repository $repository, string $url)
    {
        $this->repository = $repository;
        $this->url = $url;
    }

    public function getRepository(): Entity\Repository
    {
        return $this->repository;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
