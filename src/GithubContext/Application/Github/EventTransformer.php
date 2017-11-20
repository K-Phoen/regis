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

use Symfony\Component\HttpFoundation\Request;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Domain\Model;

/**
 * Transforms a payload sent by Github in a domain event.
 */
class EventTransformer
{
    private const TYPE_PULL_REQUEST = 'pull_request';
    private const ACTION_OPEN_PULL_REQUEST = 'opened';
    private const ACTION_CLOSE_PULL_REQUEST = 'closed';
    private const ACTION_SYNC_PULL_REQUEST = 'synchronize';

    public function transform(Request $request)
    {
        $eventType = $request->headers->get('X-GitHub-Event');

        if ($eventType === self::TYPE_PULL_REQUEST) {
            $payload = json_decode($request->getContent(), true);

            if ($payload['action'] === self::ACTION_OPEN_PULL_REQUEST) {
                return $this->transformPullRequestOpened($payload);
            } elseif ($payload['action'] === self::ACTION_SYNC_PULL_REQUEST) {
                return $this->transformPullRequestSynced($payload);
            } elseif ($payload['action'] === self::ACTION_CLOSE_PULL_REQUEST) {
                return $this->transformPullRequestClosed($payload);
            }
        }

        throw new Exception\EventNotHandled(sprintf('Event of type "%s" not handled.', $eventType));
    }

    private function transformPullRequestOpened(array $payload): Event\PullRequestOpened
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestOpened($pullRequest);
    }

    private function transformPullRequestClosed(array $payload): Event\PullRequestClosed
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestClosed($pullRequest);
    }

    private function transformPullRequestSynced(array $payload): Event\PullRequestSynced
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestSynced($pullRequest, $payload['before'], $payload['after']);
    }

    private function transformPullRequest(array $payload): Model\PullRequest
    {
        return new Model\PullRequest(
            new Model\RepositoryIdentifier($payload['repository']['owner']['login'], $payload['repository']['name']),
            (int) $payload['number'],
            $payload['pull_request']['head']['sha'],
            $payload['pull_request']['base']['sha']
        );
    }
}
