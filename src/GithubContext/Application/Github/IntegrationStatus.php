<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Github;

class IntegrationStatus
{
    const STATUS_CONTEXT = 'regis/pr';

    private $state;
    private $description;
    private $targetUrl;

    public function __construct(string $state, string $description, $targetUrl = null)
    {
        $this->state = $state;
        $this->description = $description;
        $this->targetUrl = $targetUrl;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }
}
