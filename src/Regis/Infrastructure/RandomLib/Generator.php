<?php

namespace Regis\Infrastructure\RandomLib;

use Regis\Application\Random\Generator as RandomGenerator;

class Generator implements RandomGenerator
{
    private $generator;

    public function __construct()
    {
        $factory = new \RandomLib\Factory();

        $this->generator = $factory->getMediumStrengthGenerator();
    }

    public function randomString(int $length = 24): string
    {
        return $this->generator->generateString($length);
    }
}