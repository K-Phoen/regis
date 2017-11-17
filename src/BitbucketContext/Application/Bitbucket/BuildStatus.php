<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

class BuildStatus
{
    const STATE_SUCCESSFUL = 'SUCCESSFUL';
    const STATE_INPROGRESS = 'INPROGRESS';
    const STATE_FAILED = 'FAILED';

    private $inspectionId;
    private $state;
    private $description;
    private $targetUrl;

    public static function inProgress(string $inspectionId, string $description, string $url): self
    {
        return new static($inspectionId, self::STATE_INPROGRESS, $description, $url);
    }

    public static function failed(string $inspectionId, string $description, string $url): self
    {
        return new static($inspectionId, self::STATE_FAILED, $description, $url);
    }

    public function __construct(string $inspectionId, string $state, string $description, string $url)
    {
        $this->inspectionId = $inspectionId;
        $this->state = $state;
        $this->description = $description;
        $this->targetUrl = $url;
    }

    public function key(): string
    {
        return 'regis-'.$this->inspectionId;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function url(): string
    {
        return $this->targetUrl;
    }
}
