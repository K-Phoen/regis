<?php

declare(strict_types=1);

namespace Regis\Application\Entity\Github;

use Regis\Application\Entity\Repository as BaseRepository;

class Repository extends BaseRepository
{
    public function getType(): string
    {
        return self::TYPE_GITHUB;
    }
}
