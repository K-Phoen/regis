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

namespace Regis\BitbucketContext\Application\Bitbucket;

use Regis\BitbucketContext\Domain\Model;

interface Client
{
    public function listRepositories(): \Traversable;

    public function getCloneUrl(Model\RepositoryIdentifier $repository): string;

    public function getPullRequest(Model\RepositoryIdentifier $repository, int $number): Model\PullRequest;

    public function setBuildStatus(Model\RepositoryIdentifier $repository, BuildStatus $status, string $revision): void;

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment): void;

    public function addDeployKey(Model\RepositoryIdentifier $repository, string $title, string $key): void;

    public function createWebhook(Model\RepositoryIdentifier $repository, string $url): void;
}
