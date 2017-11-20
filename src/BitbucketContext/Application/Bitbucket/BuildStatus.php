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

namespace Regis\BitbucketContext\Application\Bitbucket;

class BuildStatus
{
    public const STATE_SUCCESSFUL = 'SUCCESSFUL';
    public const STATE_INPROGRESS = 'INPROGRESS';
    public const STATE_FAILED = 'FAILED';

    private $key;
    private $state;
    private $description;
    private $targetUrl;

    public static function inProgress(string $key, string $description, string $url): self
    {
        return new static($key, self::STATE_INPROGRESS, $description, $url);
    }

    public static function failed(string $key, string $description, string $url): self
    {
        return new static($key, self::STATE_FAILED, $description, $url);
    }

    public function __construct(string $key, string $state, string $description, string $url)
    {
        $this->key = $key;
        $this->state = $state;
        $this->description = $description;
        $this->targetUrl = $url;
    }

    public function key(): string
    {
        return 'regis-'.$this->key;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function url(): string
    {
        return $this->targetUrl;
    }
}
