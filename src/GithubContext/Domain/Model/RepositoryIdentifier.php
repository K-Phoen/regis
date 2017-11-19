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

class RepositoryIdentifier
{
    private $owner;
    private $name;

    public static function fromFullName(string $fullName): self
    {
        $parts = explode('/', $fullName);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(sprintf('Invalid full name "%s"', $fullName));
        }

        return new static($parts[0], $parts[1]);
    }

    public static function fromArray(array $data): self
    {
        return new static(
            $data['owner'],
            $data['name']
        );
    }

    public function __construct(string $owner, string $name)
    {
        $this->owner = $owner;
        $this->name = $name;
    }

    public function toArray()
    {
        return [
            'owner' => $this->owner,
            'name' => $this->name,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->owner.'/'.$this->name;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getIdentifier();
    }
}
