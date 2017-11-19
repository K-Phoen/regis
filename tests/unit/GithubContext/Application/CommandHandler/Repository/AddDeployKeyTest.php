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

namespace Tests\Regis\GithubContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\RepositoryIdentifier;
use Regis\GithubContext\Domain\Repository\Repositories;

class AddDeployKeyTest extends TestCase
{
    private $githubClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\AddDeployKey */
    private $handler;

    public function setUp()
    {
        $this->githubClientFactory = $this->createMock(GithubClientFactory::class);
        $this->repositoriesRepo = $this->createMock(Repositories::class);

        $this->handler = new CommandHandler\Repository\AddDeployKey($this->githubClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsGithub()
    {
        $client = $this->createMock(GithubClient::class);
        $repository = $this->createMock(Entity\Repository::class);
        $repositoryIdentifier = RepositoryIdentifier::fromFullName('K-Phoen/test');

        $repository->method('toIdentifier')->willReturn($repositoryIdentifier);

        $this->repositoriesRepo
            ->method('find')
            ->with($repositoryIdentifier->getIdentifier())
            ->willReturn($repository);

        $this->githubClientFactory
            ->method('createForRepository')
            ->with($repository)
            ->willReturn($client);

        $client->expects($this->once())
            ->method('addDeployKey')
            ->with($repositoryIdentifier, $this->anything(), 'key content', GithubClient::READONLY_KEY);

        $this->handler->handle(new Command\Repository\AddDeployKey('K-Phoen', 'test', 'key content'));
    }
}
