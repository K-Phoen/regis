<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Regis\Kernel;

class UserAccount implements Kernel\User
{
    private $id;

    public function __construct()
    {
        $this->id = Kernel\Uuid::create();
    }

    public function accountId(): string
    {
        return $this->id;
    }
}
