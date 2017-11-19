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

namespace Tests\Regis\BitbucketContext\Application\Github;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\Bitbucket\EventTransformer;

class EventTransformerTest extends TestCase
{
    /** @var EventTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new EventTransformer();
    }

    /**
     * @expectedException \Regis\BitbucketContext\Application\Bitbucket\Exception\EventNotHandled
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
        $this->assertSame('777d3ce2ebf9', $pr->getHead());
        $this->assertSame('ba36390280a1', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertSame('{7ebc3569-bee1-48be-a539-b9f097d6ff1f}', $repo->value());
    }

    public function testPullRequestUpdatedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestUpdatedPayload());

        $this->assertInstanceOf(Event\PullRequestUpdated::class, $event);

        $pr = $event->getPullRequest();

        $this->assertSame(2, $pr->getNumber());
        $this->assertSame('5ddd1517368d', $pr->getHead());
        $this->assertSame('ba36390280a1', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertSame('{7ebc3569-bee1-48be-a539-b9f097d6ff1f}', $repo->value());
    }

    public function testPullRequestRejectedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestRejectedPayload());

        $this->assertInstanceOf(Event\PullRequestRejected::class, $event);

        $pr = $event->getPullRequest();

        $this->assertSame(2, $pr->getNumber());
        $this->assertSame('5ddd1517368d', $pr->getHead());
        $this->assertSame('ba36390280a1', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertSame('{7ebc3569-bee1-48be-a539-b9f097d6ff1f}', $repo->value());
    }

    public function testPullRequestMergedEventsAreTransformed()
    {
        $event = $this->transformer->transform($this->pullRequestMergedPayload());

        $this->assertInstanceOf(Event\PullRequestMerged::class, $event);

        $pr = $event->getPullRequest();

        $this->assertSame(2, $pr->getNumber());
        $this->assertSame('4ff0dc734173', $pr->getHead());
        $this->assertSame('ba36390280a1', $pr->getBase());

        $repo = $pr->getRepository();

        $this->assertSame('{7ebc3569-bee1-48be-a539-b9f097d6ff1f}', $repo->value());
    }

    private function pullRequestOpenedPayload(): Request
    {
        return $this->requestWithContent('pullrequest:created', <<<'PAYLOAD'
{
  "pullrequest": {
    "type": "pullrequest",
    "description": "",
    "links": {
      "decline": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/decline"
      },
      "commits": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/commits"
      },
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2"
      },
      "comments": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/comments"
      },
      "merge": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/merge"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test/pull-requests/2"
      },
      "activity": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/activity"
      },
      "diff": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/diff"
      },
      "approve": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/approve"
      },
      "statuses": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/statuses"
      }
    },
    "title": "Bitbucket integration",
    "close_source_branch": false,
    "reviewers": [],
    "destination": {
      "commit": {
        "hash": "ba36390280a1",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/ba36390280a1"
          }
        }
      },
      "branch": {
        "name": "master"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "comment_count": 0,
    "id": 2,
    "source": {
      "commit": {
        "hash": "777d3ce2ebf9",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/777d3ce2ebf9"
          }
        }
      },
      "branch": {
        "name": "bitbucket"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "state": "OPEN",
    "author": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "created_on": "2017-11-16T12:55:35.620887+00:00",
    "participants": [],
    "reason": "",
    "updated_on": "2017-11-16T12:55:35.662297+00:00",
    "merge_commit": null,
    "closed_by": null,
    "task_count": 0
  },
  "actor": {
    "username": "kphoen",
    "type": "user",
    "display_name": "Kévin Gomez",
    "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/users/kphoen"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/"
      },
      "avatar": {
        "href": "https://bitbucket.org/account/kphoen/avatar/32/"
      }
    }
  },
  "repository": {
    "scm": "git",
    "website": "",
    "name": "regis-test",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test"
      },
      "avatar": {
        "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
      }
    },
    "full_name": "kphoen/regis-test",
    "owner": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "type": "repository",
    "is_private": true,
    "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
  }
}
PAYLOAD
        );
    }

    private function pullRequestUpdatedPayload(): Request
    {
        return $this->requestWithContent('pullrequest:updated', <<<'PAYLOAD'
{
  "pullrequest": {
    "type": "pullrequest",
    "description": "",
    "links": {
      "decline": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/decline"
      },
      "commits": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/commits"
      },
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2"
      },
      "comments": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/comments"
      },
      "merge": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/merge"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test/pull-requests/2"
      },
      "activity": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/activity"
      },
      "diff": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/diff"
      },
      "approve": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/approve"
      },
      "statuses": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/statuses"
      }
    },
    "title": "Bitbucket integration",
    "close_source_branch": false,
    "reviewers": [],
    "destination": {
      "commit": {
        "hash": "ba36390280a1",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/ba36390280a1"
          }
        }
      },
      "branch": {
        "name": "master"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "comment_count": 0,
    "id": 2,
    "source": {
      "commit": {
        "hash": "5ddd1517368d",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/5ddd1517368d"
          }
        }
      },
      "branch": {
        "name": "bitbucket"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "state": "OPEN",
    "author": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "created_on": "2017-11-16T12:55:35.620887+00:00",
    "participants": [],
    "reason": "",
    "updated_on": "2017-11-16T22:39:30.356467+00:00",
    "merge_commit": null,
    "closed_by": null,
    "task_count": 0
  },
  "actor": {
    "username": "kphoen",
    "type": "user",
    "display_name": "Kévin Gomez",
    "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/users/kphoen"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/"
      },
      "avatar": {
        "href": "https://bitbucket.org/account/kphoen/avatar/32/"
      }
    }
  },
  "repository": {
    "scm": "git",
    "website": "",
    "name": "regis-test",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test"
      },
      "avatar": {
        "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
      }
    },
    "full_name": "kphoen/regis-test",
    "owner": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "type": "repository",
    "is_private": true,
    "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
  }
}
PAYLOAD
        );
    }

    private function pullRequestRejectedPayload(): Request
    {
        return $this->requestWithContent('pullrequest:rejected', <<<'PAYLOAD'
{
  "pullrequest": {
    "type": "pullrequest",
    "description": "",
    "links": {
      "decline": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/decline"
      },
      "commits": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/commits"
      },
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2"
      },
      "comments": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/comments"
      },
      "merge": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/merge"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test/pull-requests/2"
      },
      "activity": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/activity"
      },
      "diff": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/diff"
      },
      "approve": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/approve"
      },
      "statuses": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/2/statuses"
      }
    },
    "title": "Bitbucket integration",
    "close_source_branch": false,
    "reviewers": [],
    "destination": {
      "commit": {
        "hash": "ba36390280a1",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/ba36390280a1"
          }
        }
      },
      "branch": {
        "name": "master"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "comment_count": 0,
    "id": 2,
    "source": {
      "commit": {
        "hash": "5ddd1517368d",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/5ddd1517368d"
          }
        }
      },
      "branch": {
        "name": "bitbucket"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "state": "DECLINED",
    "author": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "created_on": "2017-11-16T12:55:35.620887+00:00",
    "participants": [],
    "reason": "",
    "updated_on": "2017-11-16T22:44:58.958901+00:00",
    "merge_commit": null,
    "closed_by": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "task_count": 0
  },
  "actor": {
    "username": "kphoen",
    "type": "user",
    "display_name": "Kévin Gomez",
    "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/users/kphoen"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/"
      },
      "avatar": {
        "href": "https://bitbucket.org/account/kphoen/avatar/32/"
      }
    }
  },
  "repository": {
    "scm": "git",
    "website": "",
    "name": "regis-test",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test"
      },
      "avatar": {
        "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
      }
    },
    "full_name": "kphoen/regis-test",
    "owner": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "type": "repository",
    "is_private": true,
    "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
  }
}
PAYLOAD
        );
    }

    private function pullRequestMergedPayload(): Request
    {
        return $this->requestWithContent('pullrequest:fulfilled', <<<'PAYLOAD'
{
  "pullrequest": {
    "type": "pullrequest",
    "description": "",
    "links": {
      "decline": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/decline"
      },
      "commits": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/commits"
      },
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4"
      },
      "comments": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/comments"
      },
      "merge": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/merge"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test/pull-requests/4"
      },
      "activity": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/activity"
      },
      "diff": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/diff"
      },
      "approve": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/approve"
      },
      "statuses": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/pullrequests/4/statuses"
      }
    },
    "title": "lala",
    "close_source_branch": false,
    "reviewers": [],
    "destination": {
      "commit": {
        "hash": "ba36390280a1",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/ba36390280a1"
          }
        }
      },
      "branch": {
        "name": "master"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "comment_count": 197,
    "id": 2,
    "source": {
      "commit": {
        "hash": "4ff0dc734173",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/4ff0dc734173"
          }
        }
      },
      "branch": {
        "name": "bitbucket"
      },
      "repository": {
        "full_name": "kphoen/regis-test",
        "type": "repository",
        "name": "regis-test",
        "links": {
          "self": {
            "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
          },
          "html": {
            "href": "https://bitbucket.org/kphoen/regis-test"
          },
          "avatar": {
            "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
          }
        },
        "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
      }
    },
    "state": "MERGED",
    "author": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "created_on": "2017-11-19T11:08:23.131520+00:00",
    "participants": [
      {
        "type": "participant",
        "role": "PARTICIPANT",
        "user": {
          "username": "kphoen",
          "type": "user",
          "display_name": "Kévin Gomez",
          "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
          "links": {
            "self": {
              "href": "https://api.bitbucket.org/2.0/users/kphoen"
            },
            "html": {
              "href": "https://bitbucket.org/kphoen/"
            },
            "avatar": {
              "href": "https://bitbucket.org/account/kphoen/avatar/32/"
            }
          }
        },
        "approved": false
      }
    ],
    "reason": "",
    "updated_on": "2017-11-19T11:11:36.615140+00:00",
    "merge_commit": {
      "hash": "6984bd987117",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test/commit/6984bd987117"
        }
      }
    },
    "closed_by": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "task_count": 0
  },
  "actor": {
    "username": "kphoen",
    "type": "user",
    "display_name": "Kévin Gomez",
    "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/users/kphoen"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/"
      },
      "avatar": {
        "href": "https://bitbucket.org/account/kphoen/avatar/32/"
      }
    }
  },
  "repository": {
    "scm": "git",
    "website": "",
    "name": "regis-test",
    "links": {
      "self": {
        "href": "https://api.bitbucket.org/2.0/repositories/kphoen/regis-test"
      },
      "html": {
        "href": "https://bitbucket.org/kphoen/regis-test"
      },
      "avatar": {
        "href": "https://bitbucket.org/kphoen/regis-test/avatar/32/"
      }
    },
    "full_name": "kphoen/regis-test",
    "owner": {
      "username": "kphoen",
      "type": "user",
      "display_name": "Kévin Gomez",
      "uuid": "{8c92dc12-e4a2-44aa-a6e5-4daa101664cf}",
      "links": {
        "self": {
          "href": "https://api.bitbucket.org/2.0/users/kphoen"
        },
        "html": {
          "href": "https://bitbucket.org/kphoen/"
        },
        "avatar": {
          "href": "https://bitbucket.org/account/kphoen/avatar/32/"
        }
      }
    },
    "type": "repository",
    "is_private": true,
    "uuid": "{7ebc3569-bee1-48be-a539-b9f097d6ff1f}"
  }
}
PAYLOAD
        );
    }

    private function requestWithContent(string $type, string $content): Request
    {
        return Request::create(
            '/bitbucket/webhook', 'GET',
            $parameters = [], $cookies = [], $files = [],
            $server = ['HTTP_X-Event-Key' => $type],
            $content
        );
    }
}
