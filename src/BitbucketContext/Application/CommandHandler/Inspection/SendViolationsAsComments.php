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

namespace Regis\BitbucketContext\Application\CommandHandler\Inspection;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Reporter;
use Regis\BitbucketContext\Domain\Model;

class SendViolationsAsComments
{
    private $reporter;

    public function __construct(Reporter $reporter)
    {
        $this->reporter = $reporter;
    }

    public function handle(Command\Inspection\SendViolationsAsComments $command): void
    {
        $inspection = $command->getInspection();
        $repository = $inspection->getRepository();

        if (!$inspection->hasReport()) {
            return;
        }

        $pullRequest = $inspection->getPullRequest();

        foreach ($inspection->getReport()->violations() as $violation) {
            $this->reporter->report($repository, Model\ReviewComment::fromViolation($violation), $pullRequest);
        }
    }
}
