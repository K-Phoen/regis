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

namespace Tests\Regis\BitbucketContext\Infrastructure\Github;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Entity\Repository;
use Regis\BitbucketContext\Infrastructure\Bitbucket\Client;
use Regis\BitbucketContext\Infrastructure\Bitbucket\ClientFactory;

class ClientFactoryTest extends TestCase
{
    /** @var ClientFactory */
    private $clientFactory;

    public function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);

        $this->clientFactory = new ClientFactory($logger);
    }

    public function testCreateForRepository()
    {
        $repository = $this->createMock(Repository::class);
        $owner = $this->createMock(BitbucketDetails::class);

        $repository->expects($this->once())
            ->method('getOwner')
            ->willReturn($owner);

        $client = $this->clientFactory->createForRepository($repository);

        $this->assertInstanceOf(Client::class, $client);
    }
}
