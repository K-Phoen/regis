<?php

declare(strict_types=1);

namespace Regis\Application\Vcs;

use Regis\Domain\Model;

interface Git
{
    public function getRepository(Model\Git\Repository $repository): Repository;
}
