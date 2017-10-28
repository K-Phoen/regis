<?php

declare(strict_types=1);

namespace Regis\Application\Security;

use Regis\Domain\Entity;

interface Context
{
    public function getUser(): Entity\User;
}
