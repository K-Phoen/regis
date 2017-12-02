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

namespace Tests\Regis\GithubContext\Application\CommandHandler\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Application\Github\Client;
use Regis\GithubContext\Application\Github\ClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;
use Regis\GithubContext\Domain\Repository;
use Regis\Kernel\Worker\MessagePublisher;
use Tests\Regis\Helper\ObjectManipulationHelper;

class SchedulePullRequestTest extends TestCase
{
    use ObjectManipulationHelper;

    private $messagePublisher;
    private $repositoriesRepo;
    private $inspectionsRepo;
    private $clientFactory;

    /** @var Model\RepositoryIdentifier */
    private $repositoryIdentifier;
    /** @var Entity\Repository */
    private $repository;
    private $pullRequest;

    /** @var CommandHandler\Inspection\SchedulePullRequest */
    private $handler;

    public function setUp()
    {
        $this->messagePublisher = $this->createMock(MessagePublisher::class);
        $this->repositoriesRepo = $this->createMock(Repository\Repositories::class);
        $this->inspectionsRepo = $this->createMock(Repository\PullRequestInspections::class);
        $this->clientFactory = $this->createMock(ClientFactory::class);

        $this->repositoryIdentifier = Model\RepositoryIdentifier::fromFullName('repository/test');
        $this->pullRequest = $this->createMock(Model\PullRequest::class);
        $this->pullRequest->method('getRepositoryIdentifier')->willReturn($this->repositoryIdentifier);
        $this->repository = new Entity\Repository();
        $this->setPrivateValue($this->repository, 'identifier', 'repository/test');

        $this->repositoriesRepo->method('find')->with($this->repositoryIdentifier)->willReturn($this->repository);

        $this->handler = new CommandHandler\Inspection\SchedulePullRequest(
            $this->messagePublisher,
            $this->repositoriesRepo,
            $this->inspectionsRepo,
            $this->clientFactory
        );
    }

    public function testItDoesNothingIfInspectionsAreDisabled()
    {
        $command = new Command\Inspection\SchedulePullRequest($this->pullRequest);

        $this->repository->disableInspection();

        $this->messagePublisher->expects($this->never())->method('scheduleInspection');
        $this->inspectionsRepo->expects($this->never())->method('save');

        $this->handler->handle($command);
    }

    public function testItSavesTheInspectionAndSchedulesIt()
    {
        $githubClient = $this->createMock(Client::class);
        $command = new Command\Inspection\SchedulePullRequest($this->pullRequest);

        $this->repository->enableInspection();

        $this->pullRequest->method('getNumber')->willReturn(42);
        $this->pullRequest->method('getHead')->willReturn('head-revision');
        $this->pullRequest->method('getBase')->willReturn('base-revision');

        $this->clientFactory->method('createForRepository')->with($this->repository)->willReturn($githubClient);

        $githubClient->method('getPullRequestDetails')->with($this->repositoryIdentifier, 42)->willReturn([
            'head' => [
                'repo' => [
                    'private' => false,
                    'clone_url' => 'clone-url',
                ],
            ],
        ]);

        $this->inspectionsRepo->expects($this->once())
            ->method('nextBuildNumber')
            ->with($this->repository)
            ->willReturn(2);

        $this->inspectionsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\PullRequestInspection $inspection) {
                $this->assertSame($this->repository, $inspection->getRepository());
                $this->assertSame(42, $inspection->getPullRequestNumber());
                $this->assertSame('head-revision', $inspection->getHead());
                $this->assertSame('base-revision', $inspection->getBase());
                $this->assertSame(Entity\Inspection::STATUS_SCHEDULED, $inspection->getStatus());
                $this->assertFalse($inspection->hasReport());
                $this->assertNull($inspection->getStartedAt());
                $this->assertNull($inspection->getFinishedAt());
                $this->assertNotNull($inspection->getCreatedAt());
                $this->assertEmpty($inspection->getFailureTrace());

                return true;
            }));

        $this->messagePublisher->expects($this->once())
            ->method('scheduleInspection')
            ->with($this->callback(function (array $payload) {
                $this->assertArrayHasKey('inspection_id', $payload);
                $this->assertArrayHasKey('repository', $payload);
                $this->assertArrayHasKey('revisions', $payload);

                $this->assertEquals([
                    'head' => 'head-revision',
                    'base' => 'base-revision',
                ], $payload['revisions']);

                $this->assertSame([
                    'identifier' => $this->repositoryIdentifier->getIdentifier(),
                    'clone_url' => 'clone-url',
                ], $payload['repository']);

                return true;
            }));

        $this->handler->handle($command);
    }
}
