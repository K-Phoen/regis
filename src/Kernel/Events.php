<?php

declare(strict_types=1);

namespace Regis\Kernel;

interface Events
{
    const PULL_REQUEST_OPENED = 'pull_request_opened';
    const PULL_REQUEST_CLOSED = 'pull_request_closed';
    const PULL_REQUEST_SYNCED = 'pull_request_synced';

    const INSPECTION_STARTED = 'inspection_started';
    const INSPECTION_FINISHED = 'inspection_finished';
    const INSPECTION_FAILED = 'inspection_failed';

    public function getEventName(): string;
}
