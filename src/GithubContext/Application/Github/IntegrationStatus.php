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

namespace Regis\GithubContext\Application\Github;

class IntegrationStatus
{
    public const STATUS_CONTEXT = 'regis/pr';

    private $state;
    private $description;
    private $targetUrl;

    public function __construct(string $state, string $description, ?string $targetUrl = null)
    {
        $this->state = $state;
        $this->description = $description;
        $this->targetUrl = $targetUrl;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTargetUrl(): ?string
    {
        return $this->targetUrl;
    }
}
