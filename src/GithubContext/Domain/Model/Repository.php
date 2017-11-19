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

class Repository
{
    private $identifier;
    private $publicUrl;
    private $cloneUrl;

    public static function fromArray(array $data): self
    {
        return new static(
            RepositoryIdentifier::fromArray($data['identifier']),
            $data['publicUrl'],
            $data['cloneUrl']
        );
    }

    public function __construct(RepositoryIdentifier $identifier, string $publicUrl, string $cloneUrl)
    {
        $this->identifier = $identifier;
        $this->publicUrl = $publicUrl;
        $this->cloneUrl = $cloneUrl;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->identifier->toArray(),
            'publicUrl' => $this->publicUrl,
            'cloneUrl' => $this->cloneUrl,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->identifier->getIdentifier();
    }

    public function getOwner(): string
    {
        return $this->identifier->getOwner();
    }

    public function getName(): string
    {
        return $this->identifier->getName();
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }
}
