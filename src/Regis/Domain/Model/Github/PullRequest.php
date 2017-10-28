<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Github;

use Regis\Domain\Model\Git;

class PullRequest
{
    private $repository;
    private $number;
    private $revisions;

    public static function fromArray(array $data): PullRequest
    {
        return new static(
            Git\Repository::fromArray($data['repository']),
            $data['number'],
            Git\Revisions::fromArray($data['revisions'])
        );
    }

    public function __construct(Git\Repository $repository, int $number, Git\Revisions $revisions)
    {
        $this->repository = $repository;
        $this->number = $number;
        $this->revisions = $revisions;
    }

    public function toArray()
    {
        return [
            'repository' => $this->repository->toArray(),
            'number' => $this->number,
            'revisions' => $this->revisions->toArray(),
        ];
    }

    public function getRepository(): Git\Repository
    {
        return $this->repository;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getHead(): string
    {
        return $this->revisions->getHead();
    }

    public function getBase(): string
    {
        return $this->revisions->getBase();
    }

    public function getRevisions(): Git\Revisions
    {
        return $this->revisions;
    }

    public function getRepositoryIdentifier()
    {
        return $this->getRepository()->getIdentifier();
    }

    public function __toString(): string
    {
        return sprintf('%s#%d', $this->repository->getIdentifier(), $this->number);
    }
}
