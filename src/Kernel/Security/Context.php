<?php

declare(strict_types=1);

namespace Regis\Kernel\Security;

use Regis\GithubContext\Domain\Entity;

interface Context
{
    public function getUser(): Entity\User;
}
