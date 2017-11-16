<?php

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
    const ACTION_OPEN_PULL_REQUEST = 'pullrequest:created';
    const ACTION_MERGE_PULL_REQUEST = 'pullrequest:fulfilled';
    const ACTION_REJECT_PULL_REQUEST = 'pullrequest:rejected';
    const ACTION_UPDATE_PULL_REQUEST = 'pullrequest:updated';

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
