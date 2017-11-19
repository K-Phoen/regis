<?php

declare(strict_types=1);

namespace Regis\Kernel\Security;

use Regis\AppContext\Domain\Entity;

interface Context
{
    public function getUser(): Entity\User;
}
