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

namespace Regis\BitbucketContext\Domain\Model;

class Repository
{
    private $identifier;
    private $name;
    private $cloneUrl;
    private $publicUrl;

    public static function fromArray(array $data): self
    {
        return new static(
            RepositoryIdentifier::fromArray($data['identifier']),
            $data['name'],
            $data['clone_url'],
            $data['public_url']
        );
    }

    public function __construct(RepositoryIdentifier $identifier, string $name, string $cloneUrl, string $publicUrl)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->cloneUrl = $cloneUrl;
        $this->publicUrl = $publicUrl;
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier->toArray(),
            'name' => $this->name,
            'clone_url' => $this->cloneUrl,
            'public_url' => $this->publicUrl,
        ];
    }

    public function getIdentifier(): RepositoryIdentifier
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function __toString(): string
    {
        return $this->identifier->value();
    }
}
