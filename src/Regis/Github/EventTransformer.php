<?php

declare(strict_types=1);

namespace Regis\Github;

use Symfony\Component\HttpFoundation\Request;

use Regis\Domain\Event;
use Regis\Domain\Model\Github as Model;

class EventTransformer
{
    const TYPE_PULL_REQUEST = 'pull_request';
    const ACTION_OPEN_PULL_REQUEST = 'opened';
    const ACTION_SYNC_PULL_REQUEST = 'synchronize';

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function transform(Request $request)
    {
        $eventType = $request->headers->get('X-GitHub-Event');

        if ($eventType === self::TYPE_PULL_REQUEST) {
            $payload = json_decode($request->getContent(), true);

            if ($payload['action'] === self::ACTION_OPEN_PULL_REQUEST) {
                return $this->transformPullRequestOpened($payload);
            } else if ($payload['action'] === self::ACTION_SYNC_PULL_REQUEST) {
                return $this->transformPullRequestSynced($payload);
            }
        }

        throw new \Exception(sprintf('Event of type "%s" not handled.', $eventType));
    }

    private function transformPullRequestOpened(array $payload): Event\PullRequestOpened
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestOpened($pullRequest);
    }

    private function transformPullRequestSynced(array $payload): Event\PullRequestSynced
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestSynced($pullRequest, $payload['before'], $payload['after']);
    }

    private function transformPullRequest(array $payload): Model\PullRequest
    {
        $repository = new Model\Repository(
            $payload['repository']['owner']['login'],
            $payload['repository']['name'],
            $payload['repository']['clone_url']
        );

        return new Model\PullRequest(
            $repository, (int) $payload['number'],
            $payload['pull_request']['head']['sha'],
            $payload['pull_request']['base']['sha']
        );
    }
}