<?php

declare(strict_types = 1);

namespace Regis\Infrastructure\Bundle\AuthBundle\Security;

use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use Regis\Domain\Entity;
use Regis\Application\Command;

class CommandVoter extends Voter
{
    private $rulerz;

    public function __construct(RulerZ $rulerz)
    {
        $this->rulerz = $rulerz;
    }

    protected function supports($attribute, $subject)
    {
        return strpos($attribute, 'COMMAND_') === 0;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $commandClassName = $this->attributeToCommandClass($attribute);

        if (!class_exists($commandClassName)) {
            return false;
        }

        if (!$this->commandIsSecure($commandClassName)) {
            return true;
        }

        return $this->executionAuthorized($commandClassName, $token->getUser(), $subject);
    }

    private function executionAuthorized(string $command, Entity\User $user, $targetToSecure): bool
    {
        $authorized = $command::executionAuthorizedFor($user);

        if ($authorized instanceof Specification) {
            $authorized = $this->rulerz->satisfiesSpec($targetToSecure, $authorized);
        }

        return $authorized;
    }

    private function commandIsSecure($command): bool
    {
        $implementedInterfaces = class_implements($command);
        $secureInterfaces = [Command\SecureCommand::class, Command\SecureCommandBySpecification::class];

        foreach ($secureInterfaces as $secureInterface) {
            if (in_array($secureInterface, $implementedInterfaces, true)) {
                return true;
            }
        }

        return false;
    }

    private function attributeToCommandClass(string $attribute): string
    {
        // remove COMMAND_ prefix
        $attribute = substr($attribute, 8);

        // build namespace
        $namespaceParts = explode('::', $attribute);
        $className = array_pop($namespaceParts);
        $namespaceParts = array_map(function(string $part): string {
            return ucfirst(strtolower($part));
        }, $namespaceParts);

        $namespace = '\\Regis\\Application\\Command\\'.implode('\\', $namespaceParts);

        // camelize class name
        $classNameParts = explode('_', $className);
        $classNameParts = array_map(function(string $part): string {
            return ucfirst(strtolower($part));
        }, $classNameParts);

        return $namespace . '\\' . implode('', $classNameParts);
    }
}