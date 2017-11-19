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

namespace Regis\AnalysisContext\Application\Vcs;

use Regis\AnalysisContext\Domain\Model\Git as Model;

interface Repository
{
    public function checkout(string $revision);

    public function getDiff(Model\Revisions $revisions): Model\Diff;

    /**
     * Locates a file in the repository.
     *
     * @note Currently only looks at the repository root.
     *
     * @param string $name the name of the file to locate
     *
     * @return string absolute path to the file
     *
     * @throws FileNotFound
     */
    public function locateFile(string $name): string;
}
