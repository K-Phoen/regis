<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Spec\Repository;

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
        return 'identifier LIKE :search';
    }

    public function getParameters()
    {
        return ['search' => sprintf('%%%s%%', $this->search)];
    }
}
