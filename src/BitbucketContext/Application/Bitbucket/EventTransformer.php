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

use Symfony\Component\HttpFoundation\Request;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Domain\Model;

/**
 * Transforms a payload sent by Bitbucket in a domain event.
 */
class EventTransformer
{
    private const ACTION_OPEN_PULL_REQUEST = 'pullrequest:created';
    private const ACTION_MERGE_PULL_REQUEST = 'pullrequest:fulfilled';
    private const ACTION_REJECT_PULL_REQUEST = 'pullrequest:rejected';
    private const ACTION_UPDATE_PULL_REQUEST = 'pullrequest:updated';

    public function transform(Request $request)
    {
        $eventType = $request->headers->get('X-Event-Key');
        $payload = json_decode($request->getContent(), true);

        switch ($eventType) {
            case self::ACTION_OPEN_PULL_REQUEST:
                return $this->transformPullRequestOpened($payload);
            case self::ACTION_UPDATE_PULL_REQUEST:
                return $this->transformPullRequestUpdated($payload);
            case self::ACTION_MERGE_PULL_REQUEST:
                return $this->transformPullRequestMerged($payload);
            case self::ACTION_REJECT_PULL_REQUEST:
                return $this->transformPullRequestRejected($payload);
            default:
                throw new Exception\EventNotHandled(sprintf('Event of type "%s" not handled.', $eventType));
        }
    }

    private function transformPullRequestOpened(array $payload): Event\PullRequestOpened
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestOpened($pullRequest);
    }

    private function transformPullRequestMerged(array $payload): Event\PullRequestMerged
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestMerged($pullRequest);
    }

    private function transformPullRequestRejected(array $payload): Event\PullRequestRejected
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestRejected($pullRequest);
    }

    private function transformPullRequestUpdated(array $payload): Event\PullRequestUpdated
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestUpdated($pullRequest);
    }

    private function transformPullRequest(array $payload): Model\PullRequest
    {
        return new Model\PullRequest(
            new Model\RepositoryIdentifier($payload['repository']['uuid']),
            (int) $payload['pullrequest']['id'],
            $payload['pullrequest']['source']['commit']['hash'],
            $payload['pullrequest']['destination']['commit']['hash']
        );
    }
}
