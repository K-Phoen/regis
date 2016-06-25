<?php

namespace Regis;

use Ramsey\Uuid\Uuid as UtilsUuid;

class Uuid
{
    public static function create()
    {
        return UtilsUuid::uuid4()->toString();
    }
}