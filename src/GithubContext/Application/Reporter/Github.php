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

namespace Regis\GithubContext\Application\Reporter;

use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Application\Reporter;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

class Github implements Reporter
{
    private $clientFactory;

    public function __construct(GithubClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function report(Entity\Repository $repository, Entity\Violation $violation, Model\PullRequest $pullRequest)
    {
        /** @var Entity\Repository $repository */
        $client = $this->clientFactory->createForRepository($repository);
        $client->sendComment($pullRequest, Model\ReviewComment::fromViolation($violation));
    }
}
