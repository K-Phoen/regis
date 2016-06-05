<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Github;

class PullRequest
{
    private $repository;
    private $number;
    private $head;
    private $base;

    public function __construct(Repository $repository, int $number, string $head, string $base)
    {
        $this->repository = $repository;
        $this->number = $number;
        $this->head = $head;
        $this->base = $base;
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getHead(): string
    {
        return $this->head;
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function __toString(): string
    {
        return sprintf('%s#%d', $this->repository, $this->number);
    }
}