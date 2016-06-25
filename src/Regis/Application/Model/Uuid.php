<?php

namespace Regis\Application\Model;

use Ramsey\Uuid\Uuid as UtilsUuid;

class Uuid
{
    private $uuid;

    public static function create()
    {
        return new static(UtilsUuid::uuid4()->toString());
    }

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function __toString()
    {
        return $this->uuid;
    }
}