<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

class BuildStatus
{
    const STATE_SUCCESSFUL = 'SUCCESSFUL';
    const STATE_INPROGRESS = 'INPROGRESS';
    const STATE_FAILED = 'FAILED';

    private $key;
    private $state;
    private $description;
    private $targetUrl;

    public static function inProgress(string $key, string $description, string $url): self
    {
        return new static($key, self::STATE_INPROGRESS, $description, $url);
    }

    public static function failed(string $key, string $description, string $url): self
    {
        return new static($key, self::STATE_FAILED, $description, $url);
    }

    public function __construct(string $key, string $state, string $description, string $url)
    {
        $this->key = $key;
        $this->state = $state;
        $this->description = $description;
        $this->targetUrl = $url;
    }

    public function key(): string
    {
        return 'regis-'.$this->key;
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
