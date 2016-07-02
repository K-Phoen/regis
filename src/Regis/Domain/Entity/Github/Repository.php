<?php

declare(strict_types=1);

namespace Regis\Domain\Entity\Github;

use Regis\Domain\Entity\Repository as BaseRepository;

class Repository extends BaseRepository
{
    public function getType(): string
    {
        return self::TYPE_GITHUB;
    }
}
