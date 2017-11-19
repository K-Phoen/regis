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

namespace Tests\Regis\BitbucketContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\CommandHandler;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;
use Regis\BitbucketContext\Domain\Repository\Repositories;

class AddDeployKeyTest extends TestCase
{
    private $bitbucketClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\AddDeployKey */
    private $handler;

    public function setUp()
    {
        $this->bitbucketClientFactory = $this->createMock(BitbucketClientFactory::class);
        $this->repositoriesRepo = $this->createMock(Repositories::class);

        $this->handler = new CommandHandler\Repository\AddDeployKey($this->bitbucketClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsBitbucket()
    {
        $client = $this->createMock(BitbucketClient::class);
        $repository = $this->createMock(Entity\Repository::class);
        $repositoryIdentifier = new RepositoryIdentifier('some-id');

        $repository->method('toIdentifier')->willReturn($repositoryIdentifier);
        $this->repositoriesRepo->method('find')->with('some-id')->willReturn($repository);
        $this->bitbucketClientFactory->method('createForRepository')->with($repository)->willReturn($client);

        $client->expects($this->once())
            ->method('addDeployKey')
            ->with($repositoryIdentifier, $this->anything(), 'key content');

        $this->handler->handle(new Command\Repository\AddDeployKey($repositoryIdentifier, 'key content'));
    }
}
