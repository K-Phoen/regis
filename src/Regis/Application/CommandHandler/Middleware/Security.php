<?php

declare(strict_types = 1);

namespace Regis\Application\CommandHandler\Middleware;

use League\Tactician\Middleware;
use Regis\Application\Command\SecureCommandBySpecification;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\Application\Command\SecureCommand;
use Regis\Application\Security\Context as SecurityContext;
use Regis\Application\Security\Exception\AccessDenied;

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
        if (($command instanceof SecureCommand || $command instanceof SecureCommandBySpecification) && !$this->executionAuthorized($command)) {
            throw AccessDenied::forCommand($command);
        }

        $next($command);
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