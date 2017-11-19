<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

class GithubProfile
{
    private $id;
    private $user;
    private $username;

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
