<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Application\Github;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\Github\EventTransformer;

class EventTransformerTest extends TestCase
{
    /** @var EventTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new EventTransformer();
    }

    /**
     * @expectedException \Regis\GithubContext\Application\Github\Exception\EventNotHandled
     * @expectedExceptionMessage Event of type "unknown_event_type" not handled.
     */
    public function testUnknownEventsAreRejected()
    {
        $this->transformer->transform($this->requestWithContent('unknown_event_type', 'dummy_payload'));
    }

    public function testPullRequestOpenedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestOpenedPayload());

        $this->assertInstanceOf(Event\PullRequestOpened::class, $event);

        $pr = $event->getPullRequest();

        $this->assertSame(2, $pr->getNumber());
        $this->assertSame('57dee1bee0cf795d2a1dcf8616320618e72807a8', $pr->getHead());
        $this->assertSame('1d6206cb1f76682a9f272e0547721a2aadc58554', $pr->getBase());

        $repo = $pr->getRepositoryIdentifier();

        $this->assertSame('test', $repo->getName());
        $this->assertSame('K-Phoen', $repo->getOwner());
    }

    public function testPullRequestSyncedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestSyncedPayload());

        $this->assertInstanceOf(Event\PullRequestSynced::class, $event);

        $this->assertSame('1d6206cb1f76682a9f272e0547721a2aadc58554', $event->getBefore());
        $this->assertSame('57dee1bee0cf795d2a1dcf8616320618e72807a8', $event->getAfter());

        $pr = $event->getPullRequest();

        $this->assertSame(2, $pr->getNumber());
        $this->assertSame('57dee1bee0cf795d2a1dcf8616320618e72807a8', $pr->getHead());
        $this->assertSame('1d6206cb1f76682a9f272e0547721a2aadc58554', $pr->getBase());

        $repo = $pr->getRepositoryIdentifier();

        $this->assertSame('test', $repo->getName());
        $this->assertSame('K-Phoen', $repo->getOwner());
    }

    public function testPullRequestClosedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestClosedPayload());

        $this->assertInstanceOf(Event\PullRequestClosed::class, $event);

        $pr = $event->getPullRequest();

        $this->assertSame(2, $pr->getNumber());
        $this->assertSame('57dee1bee0cf795d2a1dcf8616320618e72807a8', $pr->getHead());
        $this->assertSame('1d6206cb1f76682a9f272e0547721a2aadc58554', $pr->getBase());

        $repo = $pr->getRepositoryIdentifier();

        $this->assertSame('test', $repo->getName());
        $this->assertSame('K-Phoen', $repo->getOwner());
    }

    private function pullRequestOpenedPayload(): Request
    {
        return $this->requestWithContent('pull_request', <<<'PAYLOAD'
{
  "action": "opened",
  "number": 2,
  "pull_request": {
    "url": "https://api.github.com/repos/K-Phoen/test/pulls/2",
    "id": 72605898,
    "number": 2,
    "state": "open",
    "locked": false,
    "title": "Moar style violations",
    "head": {
      "label": "K-Phoen:moar-style-violations",
      "ref": "moar-style-violations",
      "sha": "57dee1bee0cf795d2a1dcf8616320618e72807a8"
    },
    "base": {
      "label": "K-Phoen:master",
      "ref": "master",
      "sha": "1d6206cb1f76682a9f272e0547721a2aadc58554"
    }
  },
  "repository": {
    "id": 60372801,
    "name": "test",
    "full_name": "K-Phoen/test",
    "owner": {
      "login": "K-Phoen"
    },
    "private": false,
    "clone_url": "https://github.com/K-Phoen/test.git",
    "ssh_url": "git@github.com:K-Phoen/test.git"
  }
}
PAYLOAD
        );
    }

    private function pullRequestSyncedPayload(): Request
    {
        return $this->requestWithContent('pull_request', <<<'PAYLOAD'
{
  "action": "synchronize",
  "number": 2,
  "pull_request": {
    "url": "https://api.github.com/repos/K-Phoen/test/pulls/2",
    "id": 72605898,
    "number": 2,
    "state": "open",
    "locked": false,
    "title": "Moar style violations",
    "head": {
      "label": "K-Phoen:moar-style-violations",
      "ref": "moar-style-violations",
      "sha": "57dee1bee0cf795d2a1dcf8616320618e72807a8"
    },
    "base": {
      "label": "K-Phoen:master",
      "ref": "master",
      "sha": "1d6206cb1f76682a9f272e0547721a2aadc58554"
    }
  },
  "before": "1d6206cb1f76682a9f272e0547721a2aadc58554",
  "after": "57dee1bee0cf795d2a1dcf8616320618e72807a8",
  "repository": {
    "id": 60372801,
    "name": "test",
    "full_name": "K-Phoen/test",
    "owner": {
      "login": "K-Phoen"
    },
    "private": false,
    "clone_url": "https://github.com/K-Phoen/test.git",
    "ssh_url": "git@github.com:K-Phoen/test.git"
  }
}
PAYLOAD
        );
    }

    private function pullRequestClosedPayload(): Request
    {
        return $this->requestWithContent('pull_request', <<<'PAYLOAD'
{
  "action": "closed",
  "number": 2,
  "pull_request": {
    "url": "https://api.github.com/repos/K-Phoen/test/pulls/2",
    "id": 72605898,
    "number": 2,
    "state": "open",
    "locked": false,
    "title": "Moar style violations",
    "head": {
      "label": "K-Phoen:moar-style-violations",
      "ref": "moar-style-violations",
      "sha": "57dee1bee0cf795d2a1dcf8616320618e72807a8"
    },
    "base": {
      "label": "K-Phoen:master",
      "ref": "master",
      "sha": "1d6206cb1f76682a9f272e0547721a2aadc58554"
    }
  },
  "repository": {
    "id": 60372801,
    "name": "test",
    "full_name": "K-Phoen/test",
    "owner": {
      "login": "K-Phoen"
    },
    "private": false,
    "clone_url": "https://github.com/K-Phoen/test.git",
    "ssh_url": "git@github.com:K-Phoen/test.git"
  }
}
PAYLOAD
        );
    }

    private function requestWithContent(string $type, string $content): Request
    {
        return Request::create(
            '/github/webhook', 'GET',
            $parameters = [], $cookies = [], $files = [],
            $server = ['HTTP_X-GitHub-Event' => $type],
            $content
        );
    }
}
