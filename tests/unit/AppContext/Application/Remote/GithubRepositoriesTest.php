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

namespace Tests\Regis\AppContext\Infrastructure\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\User;
use Regis\AppContext\Application\Remote\GithubRepositories;
use Regis\AppContext\Application\Remote\Repositories;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Domain\Entity\GithubDetails;
use Regis\GithubContext\Domain\Repository as GithubRepository;
use Regis\GithubContext\Domain\Model as GithubModel;
use Regis\GithubContext\Domain\Repository\Exception as GithubException;
use Regis\Kernel;
use Regis\AppContext\Domain\Model;
use Regis\AppContext\Domain\Entity;
use Tests\Regis\Helper\ObjectManipulationHelper;

class GithubRepositoriesTest extends TestCase
{
    use ObjectManipulationHelper;

    const ACCOUNT_ID = 'account-id';

    /** @var GithubClientFactory */
    private $ghClientFactory;
    /** @var GithubRepository\Users */
    private $usersRepo;
    /** @var Kernel\User */
    private $user;

    /** @var GithubRepositories */
    private $githubRepositories;

    public function setUp()
    {
        $this->ghClientFactory = $this->createMock(GithubClientFactory::class);
        $this->usersRepo = $this->createMock(GithubRepository\Users::class);
        $this->user = new User();

        $this->setPrivateValue($this->user, 'id', self::ACCOUNT_ID);

        $this->githubRepositories = new GithubRepositories($this->ghClientFactory, $this->usersRepo);
    }

    public function testItImplementsTheRightInterfaces()
    {
        $this->assertInstanceOf(Repositories::class, $this->githubRepositories);
    }

    public function testItReturnsAnEmptyListIfTheUserHasNoGithubProfile()
    {
        $this->usersRepo->method('findByAccountId')->with(self::ACCOUNT_ID)->willThrowException(new GithubException\NotFound());

        $repositories = $this->githubRepositories->forUser($this->user);

        $this->assertCount(0, $repositories);
    }

    public function testItFetchesTheRepositoriesFromGithubWhenTheUserHasAGithubProfile()
    {
        $githubProfile = $this->createMock(GithubDetails::class);
        $githubClient = $this->createMock(GithubClient::class);
        $githubRepository = new GithubModel\Repository(
            GithubModel\RepositoryIdentifier::fromFullName('K-Phoen/regis-test'),
            'public-url',
            'clone-url'
        );

        $this->usersRepo->method('findByAccountId')->with(self::ACCOUNT_ID)->willReturn($githubProfile);
        $this->ghClientFactory->method('createForUser')->with($githubProfile)->willReturn($githubClient);
        $githubClient->method('listRepositories')->willReturn(new \ArrayIterator([$githubRepository]));

        $repositories = iterator_to_array($this->githubRepositories->forUser($this->user));

        $this->assertCount(1, $repositories);

        /** @var Model\Repository $repository */
        $repository = current($repositories);
        $this->assertInstanceOf(Model\Repository::class, $repository);
        $this->assertSame('K-Phoen/regis-test', $repository->getIdentifier());
        $this->assertSame('K-Phoen/regis-test', $repository->getName());
        $this->assertSame('public-url', $repository->getPublicUrl());
        $this->assertSame(Entity\Repository::TYPE_GITHUB, $repository->getType());
    }
}
