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

namespace Regis\AppContext\Application\CommandHandler\Middleware;

use League\Tactician\Middleware;
use RulerZ\RulerZ;
use Regis\AppContext\Application\Command\SecureCommandBySpecification;
use Regis\AppContext\Application\Command\SecureCommand;
use Regis\Kernel\Security\Context as SecurityContext;
use Regis\Kernel\Security\Exception\AccessDenied;

class Security implements Middleware
{
    private $rulerz;
    private $securityContext;

    public function __construct(RulerZ $rulerz, SecurityContext $securityContext)
    {
        $this->rulerz = $rulerz;
        $this->securityContext = $securityContext;
    }

    public function execute($command, callable $next)
    {
        if ($this->commandIsSecure($command) && !$this->executionAuthorized($command)) {
            throw AccessDenied::forCommand($command);
        }

        return $next($command);
    }

    private function commandIsSecure($command): bool
    {
        return $command instanceof SecureCommand || $command instanceof SecureCommandBySpecification;
    }

    /**
     * @param SecureCommand|SecureCommandBySpecification $command
     *
     * @return bool
     */
    private function executionAuthorized($command): bool
    {
        $user = $this->securityContext->getUser();
        $authorized = $command->executionAuthorizedFor($user);

        if ($command instanceof SecureCommandBySpecification) {
            $authorized = $this->rulerz->satisfiesSpec($command->getTargetToSecure(), $authorized);
        }

        return $authorized;
    }
}
