<?php

declare(strict_types=1);

namespace Regis\Domain\Model;

class PullRequest
{
    private $repository;
    private $number;
    private $head;
    private $base;
    /** @var Commit[] */
    private $commits = [];

    public function withCommits(array $commits): PullRequest
    {
        return new static($this->repository, $this->number, $this->head, $this->base, $commits);
    }

    public function __construct(Repository $repository, int $number, string $head, string $base, array $commits = [])
    {
        $this->repository = $repository;
        $this->number = $number;
        $this->head = $head;
        $this->base = $base;
        $this->commits = $commits;
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

    /**
     * @return Commit[]
     */
    public function getCommits(): array 
    {
        return $this->commits;
    }
}