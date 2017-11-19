<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Remote;

use Regis\AppContext\Domain\Entity;

interface Actions
{
    public function createWebhook(Entity\Repository $repository, string $hookUrl);
}
