<?php

declare(strict_types=1);

namespace Regis\Domain;

interface Inspection
{
    function inspectDiff(Model\Git\Diff $diff): \Traversable;
}