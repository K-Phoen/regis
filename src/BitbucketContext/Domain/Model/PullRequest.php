<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Model;

class PullRequest
{
    private $repository;
    private $number;
    private $head;
    private $base;

    public static function fromArray(array $data): self
    {
        return new static(
            RepositoryIdentifier::fromArray($data['repository_identifier']),
            $data['number'],
            $data['head'],
            $data['base']
        );
    }

    public function __construct(RepositoryIdentifier $repository, int $number, string $head, string $base)
    {
        $this->repository = $repository;
        $this->number = $number;
        $this->head = $head;
        $this->base = $base;
    }

    public function toArray()
    {
        return [
            'repository_identifier' => $this->repository->toArray(),
            'number' => $this->number,
            'head' => $this->head,
            'base' => $this->base,
        ];
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

    public function getRepository(): RepositoryIdentifier
    {
        return $this->repository;
    }

    public function __toString(): string
    {
        return sprintf('%s#%d', $this->repository, $this->number);
    }
}
