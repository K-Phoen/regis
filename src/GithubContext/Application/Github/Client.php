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

namespace Regis\GithubContext\Application\Github;

use Regis\GithubContext\Domain\Model;

interface Client
{
    public const INTEGRATION_PENDING = 'pending';
    public const INTEGRATION_SUCCESS = 'success';
    public const INTEGRATION_FAILURE = 'failure';
    public const INTEGRATION_ERROR = 'error';

    public const READONLY_KEY = 'readonly_key';
    public const WRITE_KEY = 'write_key';

    public function setIntegrationStatus(Model\RepositoryIdentifier $repository, string $head, IntegrationStatus $status);

    public function addDeployKey(Model\RepositoryIdentifier $repository, string $title, string $key, string $type);

    public function createWebhook(Model\RepositoryIdentifier $repository, string $url, $secret = null);

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment);

    public function listRepositories(): \Traversable;

    public function getPullRequestDetails(Model\RepositoryIdentifier $repository, int $number): array;
}
