<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Model;

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

    public function toArray(): array
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

    public function getRepositoryIdentifier(): RepositoryIdentifier
    {
        return $this->repository;
    }

    public function __toString(): string
    {
        return sprintf('%s#%d', $this->repository->getIdentifier(), $this->number);
    }
}
