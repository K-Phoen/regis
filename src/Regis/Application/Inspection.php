<?php

declare(strict_types=1);

namespace Regis\Application;

interface Inspection
{
    function getType(): string;

    function inspectDiff(Model\Git\Diff $diff): \Traversable;
}