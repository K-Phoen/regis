<?php

declare(strict_types=1);

namespace Regis\Github;

use Symfony\Component\HttpFoundation\Request;

use Regis\Domain\Event\PullRequestOpened;
use Regis\Domain\Model\Github as Model;

class EventTransformer
{
    const TYPE_PULL_REQUEST = 'pull_request';

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
            
            return $this->transformPullRequest($payload);
        }

        throw new \Exception(sprintf('Event of type "%s" not handled.', $eventType));
    }

    private function transformPullRequest(array $payload): PullRequestOpened
    {
        $repository = new Model\Repository($payload['repository']['owner']['login'], $payload['repository']['name'], $payload['repository']['clone_url']);
        $pullRequest = new Model\PullRequest(
            $repository, (int) $payload['number'],
            $payload['pull_request']['head']['sha'], $payload['pull_request']['base']['sha']
        );

        return new PullRequestOpened($pullRequest);
    }
}