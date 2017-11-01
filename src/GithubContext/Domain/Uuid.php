<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain;

use Ramsey\Uuid\Uuid as UtilsUuid;

class Uuid
{
    public static function create()
    {
        return UtilsUuid::uuid4()->toString();
    }
}
