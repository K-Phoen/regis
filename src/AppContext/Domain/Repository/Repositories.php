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

namespace Regis\AppContext\Domain\Repository;

use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity;

interface Repositories
{
    public const MODE_FETCH_NOTHING = 0;
    public const MODE_FETCH_RELATIONS = 1;

    public function save(Entity\Repository $team): void;

    public function matching(Specification $spec): \Traversable;

    /**
     * @throws \Regis\AppContext\Domain\Repository\Exception\NotFound
     */
    public function find(string $id, $mode = self::MODE_FETCH_NOTHING): Entity\Repository;

    public function findByIdentifier(string $type, string $identifier): Entity\Repository;
}
