<?php

namespace Tests\Regis\Github;

use Symfony\Component\HttpFoundation\Request;

use Regis\Application\Event;
use Regis\Github\EventTransformer;

class EventTransformerTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new EventTransformer();
    }

    /**
     * @expectedException \Regis\Github\Exception\EventNotHandled
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
        $this->assertEquals(Event::PULL_REQUEST_OPENED, $event->getEventName());

        $pr = $event->getPullRequest();

        $this->assertEquals(2, $pr->getNumber());
        $this->assertEquals('57dee1bee0cf795d2a1dcf8616320618e72807a8', $pr->getHead());
        $this->assertEquals('1d6206cb1f76682a9f272e0547721a2aadc58554', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertEquals('test', $repo->getName());
        $this->assertEquals('K-Phoen', $repo->getOwner());
        $this->assertEquals('https://github.com/K-Phoen/test.git', $repo->getCloneUrl());
    }

    public function testPullRequestSyncedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestSyncedPayload());

        $this->assertInstanceOf(Event\PullRequestSynced::class, $event);
        $this->assertEquals(Event::PULL_REQUEST_SYNCED, $event->getEventName());

        $this->assertEquals('1d6206cb1f76682a9f272e0547721a2aadc58554', $event->getBefore());
        $this->assertEquals('57dee1bee0cf795d2a1dcf8616320618e72807a8', $event->getAfter());

        $pr = $event->getPullRequest();

        $this->assertEquals(2, $pr->getNumber());
        $this->assertEquals('57dee1bee0cf795d2a1dcf8616320618e72807a8', $pr->getHead());
        $this->assertEquals('1d6206cb1f76682a9f272e0547721a2aadc58554', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertEquals('test', $repo->getName());
        $this->assertEquals('K-Phoen', $repo->getOwner());
        $this->assertEquals('https://github.com/K-Phoen/test.git', $repo->getCloneUrl());
    }

    public function testPullRequestClosedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestClosedPayload());
        $this->assertEquals(Event::PULL_REQUEST_CLOSED, $event->getEventName());

        $this->assertInstanceOf(Event\PullRequestClosed::class, $event);

        $pr = $event->getPullRequest();

        $this->assertEquals(2, $pr->getNumber());
        $this->assertEquals('57dee1bee0cf795d2a1dcf8616320618e72807a8', $pr->getHead());
        $this->assertEquals('1d6206cb1f76682a9f272e0547721a2aadc58554', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertEquals('test', $repo->getName());
        $this->assertEquals('K-Phoen', $repo->getOwner());
        $this->assertEquals('https://github.com/K-Phoen/test.git', $repo->getCloneUrl());
    }

    private function pullRequestOpenedPayload(): Request
    {
        return $this->requestWithContent('pull_request', <<<PAYLOAD
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
    "clone_url": "https://github.com/K-Phoen/test.git"
  }
}
PAYLOAD
        );
    }

    private function pullRequestSyncedPayload(): Request
    {
        return $this->requestWithContent('pull_request', <<<PAYLOAD
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
    "clone_url": "https://github.com/K-Phoen/test.git"
  }
}
PAYLOAD
        );
    }

    private function pullRequestClosedPayload(): Request
    {
        return $this->requestWithContent('pull_request', <<<PAYLOAD
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
    "clone_url": "https://github.com/K-Phoen/test.git"
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
