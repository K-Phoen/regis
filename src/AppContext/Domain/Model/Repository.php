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

namespace Regis\AppContext\Domain\Model;

class Repository
{
    private $identifier;
    private $name;
    private $type;
    private $publicUrl;

    public function __construct(string $identifier, string $name, string $publicUrl, string $type)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->publicUrl = $publicUrl;
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'name' => $this->name,
            'public_url' => $this->publicUrl,
            'type' => $this->type,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
