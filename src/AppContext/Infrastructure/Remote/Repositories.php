<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Remote;

use Regis\AppContext\Domain\Model;
use Regis\Kernel;

interface Repositories
{
    /**
     * @return Model\Repository[]
     */
    public function forUser(Kernel\User $user);
}
