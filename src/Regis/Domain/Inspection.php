<?php

namespace Regis\Domain;

use Gitonomy\Git as Gitonomy;

interface Inspection
{
    function inspectDiff(Gitonomy\Diff\Diff $diff): \Traversable;
}