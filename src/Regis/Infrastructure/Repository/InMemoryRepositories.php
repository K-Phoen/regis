<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Repository;

use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

class InMemoryRepositories implements Repository\Repositories
{
    private $repositories = [];
    private $rulerz;

    public function __construct(RulerZ $rulerz, array $repositories)
    {
        $this->rulerz = $rulerz;

        /** @var Entity\Repository $repository */
        foreach ($repositories as $repository) {
            $this->repositories[$repository->getIdentifier()] = $repository;
        }
    }

    public function matching(Specification $spec): \Traversable
    {
        return $this->rulerz->filterSpec($this->repositories, $spec);
    }

    public function save(Entity\Repository $team)
    {
        $this->repositories[$team->getIdentifier()] = $team;
    }

    public function find(string $id, $mode = self::MODE_FETCH_NOTHING): Entity\Repository
    {
        if (!array_key_exists($id, $this->repositories)) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $this->repositories[$id];
    }
}
