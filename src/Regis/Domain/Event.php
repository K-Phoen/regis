<?php

declare(strict_types=1);

namespace Regis\Domain;

interface Event
{
    const PULL_REQUEST_OPENED = 'pull_request_opened';
    const PULL_REQUEST_SYNCED = 'pull_request_synced';

    function getEventName(): string;
}