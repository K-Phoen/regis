<?php

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
