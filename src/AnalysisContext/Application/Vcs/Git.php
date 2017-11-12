<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Vcs;

use Regis\AnalysisContext\Domain\Model\Git as Model;

interface Git
{
    public function getRepository(Model\Repository $repository): Repository;
}
