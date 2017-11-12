<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Spec\User;

use RulerZ\Spec\AbstractSpecification;

class Matches extends AbstractSpecification
{
    private $search;

    public function __construct(string $search)
    {
        $this->search = $search;
    }

    public function getRule()
    {
        return 'username LIKE :search';
    }

    public function getParameters()
    {
        return ['search' => sprintf('%%%s%%', $this->search)];
    }
}
