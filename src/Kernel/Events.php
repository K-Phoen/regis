<?php

declare(strict_types=1);

namespace Regis\Kernel;

interface Events
{
    const INSPECTION_STARTED = 'inspection_started';
    const INSPECTION_FINISHED = 'inspection_finished';
    const INSPECTION_FAILED = 'inspection_failed';

    public function getEventName(): string;
}
