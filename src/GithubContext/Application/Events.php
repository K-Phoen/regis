<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application;

interface Events
{
    const PULL_REQUEST_OPENED = 'pull_request_opened';
    const PULL_REQUEST_CLOSED = 'pull_request_closed';
    const PULL_REQUEST_SYNCED = 'pull_request_synced';
}
