<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Model;

class PullRequest
{
    private $repository;
    private $number;
    private $head;
    private $base;

    public static function fromArray(array $data): PullRequest
    {
        return new static(
            Repository::fromArray($data['repository']),
            $data['number'],
            $data['head'],
            $data['base']
        );
    }

    public function __construct(Repository $repository, int $number, string $head, string $base)
    {
        $this->repository = $repository;
        $this->number = $number;
        $this->head = $head;
        $this->base = $base;
    }

    public function toArray()
    {
        return [
            'repository' => $this->repository->toArray(),
            'number' => $this->number,
            'head' => $this->head,
            'base' => $this->base,
        ];
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

    public function getRepositoryIdentifier()
    {
        return $this->repository->getIdentifier();
    }

    public function __toString(): string
    {
        return sprintf('%s#%d', $this->repository->getIdentifier(), $this->number);
    }
}
