<?php

declare(strict_types=1);

namespace Regis\Kernel\Monolog;

class RequestIdProcessor
{
    /** @var string */
    private $requestId;

    public function __construct()
    {
        $this->requestId = uniqid('request_', true);
    }

    public function processRecord(array $record): array
    {
        if (empty($record['extra']['request_id'])) {
            $record['extra']['request_id'] = $this->requestId;
        }

        return $record;
    }
}
