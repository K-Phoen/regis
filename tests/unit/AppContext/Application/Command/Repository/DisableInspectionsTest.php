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

namespace Tests\Regis\AppContext\Application\Command\Repository;

use PHPUnit\Framework\TestCase;
use RulerZ\Spec\Specification;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;

class DisableInspectionsTest extends TestCase
{
    public function testCommandIsSecured()
    {
        $user = $this->createMock(Entity\User::class);
        $repo = $this->createMock(Entity\Repository::class);

        $command = new Command\Repository\DisableInspections($repo);

        $this->assertInstanceOf(Command\SecureCommandBySpecification::class, $command);
        $this->assertInstanceOf(Specification::class, $command::executionAuthorizedFor($user));
        $this->assertSame($repo, $command->getTargetToSecure());
    }
}
