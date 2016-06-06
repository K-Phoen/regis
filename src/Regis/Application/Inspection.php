<?php

declare(strict_types=1);

namespace Regis\Application;

interface Inspection
{
    function inspectDiff(Model\Git\Diff $diff): \Traversable;
}