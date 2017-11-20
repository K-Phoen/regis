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

namespace Regis\BitbucketContext\Application\Inspection;

use Predis\ClientInterface;
use Regis\BitbucketContext\Domain\Model;

class ViolationsCache
{
    private $redis;

    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
    }

    public function has(Model\ReviewComment $comment, Model\PullRequest $pullRequest): bool
    {
        return (bool) $this->redis->sismember($this->getPullRequestKey($pullRequest), $this->getViolationKey($comment));
    }

    public function save(Model\ReviewComment $comment, Model\PullRequest $pullRequest): void
    {
        $this->redis->sadd($this->getPullRequestKey($pullRequest), [$this->getViolationKey($comment)]);
    }

    public function clear(Model\PullRequest $pullRequest): void
    {
        $this->redis->del([$this->getPullRequestKey($pullRequest)]);
    }

    private function getPullRequestKey(Model\PullRequest $pullRequest): string
    {
        return sprintf('%s#%d', $pullRequest->getRepository()->value(), $pullRequest->getNumber());
    }

    private function getViolationKey(Model\ReviewComment $comment): string
    {
        return md5(
            sprintf('%s:%d -- %s', $comment->file(), $comment->line(), $comment->content())
        );
    }
}
