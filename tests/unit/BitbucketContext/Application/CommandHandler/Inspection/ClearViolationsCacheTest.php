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

namespace Tests\Regis\BitbucketContext\Application\CommandHandler\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\CommandHandler;
use Regis\BitbucketContext\Domain\Model;
use Tests\Regis\Helper\ObjectManipulationHelper;
use Regis\BitbucketContext\Application\Inspection\ViolationsCache;

class ClearViolationsCacheTest extends TestCase
{
    use ObjectManipulationHelper;

    private $violationsCache;

    /** @var CommandHandler\Inspection\ClearViolationsCache */
    private $handler;

    public function setUp()
    {
        $this->violationsCache = $this->createMock(ViolationsCache::class);

        $this->handler = new CommandHandler\Inspection\ClearViolationsCache($this->violationsCache);
    }

    public function testItClearsTheViolationCache()
    {
        $pullRequest = $this->createMock(Model\PullRequest::class);
        $command = new Command\Inspection\ClearViolationsCache($pullRequest);

        $this->violationsCache->expects($this->once())
            ->method('clear')
            ->with($pullRequest);

        $this->handler->handle($command);
    }
}
