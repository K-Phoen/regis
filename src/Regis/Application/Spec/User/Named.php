<?php

declare(strict_types=1);

namespace Regis\Application\Spec\User;

use RulerZ\Spec\AbstractSpecification;

class Named extends AbstractSpecification
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getRule()
    {
        return 'username = :username';
    }

    public function getParameters()
    {
        return ['username' => $this->username];
    }
}