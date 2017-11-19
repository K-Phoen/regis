<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Spec\User;

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
        return 'githubProfile.username = :username OR bitbucketProfile.username = :username';
    }

    public function getParameters()
    {
        return ['username' => $this->username];
    }
}
