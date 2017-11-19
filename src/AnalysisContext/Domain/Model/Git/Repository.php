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

namespace Regis\AnalysisContext\Domain\Model\Git;

class Repository
{
    private $cloneUrl;
    private $identifier;

    public static function fromArray(array $data): self
    {
        return new static(
            $data['identifier'],
            $data['clone_url']
        );
    }

    public function __construct(string $identifier, string $cloneUrl)
    {
        $this->cloneUrl = $cloneUrl;
        $this->identifier = $identifier;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->identifier,
            'clone_url' => $this->cloneUrl,
        ];
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
