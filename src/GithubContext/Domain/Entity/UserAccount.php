<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Regis\Kernel;

class UserAccount implements Kernel\User
{
    private $id;
    private $repositories;
    private $details;

    public function __construct()
    {
        $this->id = Kernel\Uuid::create();
    }

    public function accountId(): string
    {
        return $this->id;
    }

    public function getDetails(): GithubDetails
    {
        return $this->details;
    }

    public function getRepositories(): \Traversable
    {
        return $this->repositories;
    }
}
