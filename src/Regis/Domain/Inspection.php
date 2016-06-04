<?php

declare(strict_types=1);

namespace Regis\Domain;

interface Inspection
{
    function inspectDiff(Model\Diff $diff): \Traversable;
}