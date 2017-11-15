<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Regis\Kernel;

class UserAccount implements Kernel\User
{
    private $id;
    private $details;

    public function __construct()
    {
        $this->id = Kernel\Uuid::create();
    }

    public function accountId(): string
    {
        return $this->id;
    }

    public function getDetails(): BitbucketDetails
    {
        return $this->details;
    }
}
