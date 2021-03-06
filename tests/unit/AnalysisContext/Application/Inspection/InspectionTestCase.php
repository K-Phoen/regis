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

namespace Tests\Regis\AnalysisContext\Application\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Vcs\Repository;
use Regis\AnalysisContext\Domain\Model;

abstract class InspectionTestCase extends TestCase
{
    protected const REPOSITORY_ROOT = 'some-repo-root';

    protected function repository(string $root = self::REPOSITORY_ROOT): Repository
    {
        $repo = $this->createMock(Repository::class);
        $repo->method('root')->willReturn($root);

        return $repo;
    }

    protected function diff(array $addedPhpFiles = []): Model\Git\Diff
    {
        $diff = $this->createMock(Model\Git\Diff::class);
        $diff->method('getAddedPhpFiles')->willReturn(new \ArrayIterator($addedPhpFiles));

        return $diff;
    }

    protected function file(string $name): Model\Git\Diff\File
    {
        $diff = $this->createMock(Model\Git\Diff\File::class);
        $diff->method('getNewName')->willReturn($name);

        return $diff;
    }
}
