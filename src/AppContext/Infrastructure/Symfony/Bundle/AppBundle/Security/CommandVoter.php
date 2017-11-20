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

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Security;

use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Application\Command;

class CommandVoter extends Voter
{
    private $rulerz;

    public function __construct(RulerZ $rulerz)
    {
        $this->rulerz = $rulerz;
    }

    protected function supports($attribute, $subject): bool
    {
        return strpos($attribute, 'COMMAND_') === 0;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
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
        $namespaceParts = array_map(function (string $part): string {
            return ucfirst(strtolower($part));
        }, $namespaceParts);

        $namespace = '\\Regis\\AppContext\Application\\Command\\'.implode('\\', $namespaceParts);

        // camelize class name
        $classNameParts = explode('_', $className);
        $classNameParts = array_map(function (string $part): string {
            return ucfirst(strtolower($part));
        }, $classNameParts);

        return $namespace.'\\'.implode('', $classNameParts);
    }
}
