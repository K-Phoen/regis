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

namespace Tests\Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Security;

use PHPUnit\Framework\TestCase;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Security\CommandVoter;

class CommandVoterTest extends TestCase
{
    private $rulerz;
    /** @var VoterInterface */
    private $voter;
    private $token;
    private $user;

    public function setUp()
    {
        $this->rulerz = $this->createMock(RulerZ::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->user = $this->createMock(Entity\User::class);

        $this->token->method('getUser')->willReturn($this->user);

        $this->voter = new CommandVoter($this->rulerz);
    }

    /**
     * @dataProvider unsupportedAttributesProvider
     */
    public function testItAbstainsForUnsupportedAttributes($attribute)
    {
        $subject = 'does not matter';

        $result = $this->voter->vote($this->token, $subject, [$attribute]);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testItDeniesTheAccessIfTheCommandDoesNotExist()
    {
        $subject = 'does not matter';

        $result = $this->voter->vote($this->token, $subject, ['COMMAND_THAT_DOES_NOT_EXIST']);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testItAllowsTheAccessIfTheCommandIsNotSecure()
    {
        $subject = 'does not matter';

        $result = $this->voter->vote($this->token, $subject, ['COMMAND_TEAM::CREATE']);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testItDelegatesTheAuthorisationCheckToRulerZForCommandsSecuredBySpecification()
    {
        $subject = 'does not matter';

        $this->rulerz->expects($this->once())
            ->method('satisfiesSpec')
            ->with($subject, $this->callback(function (Specification $specification) {
                return true;
            }))
            ->willReturn(true);

        $result = $this->voter->vote($this->token, $subject, ['COMMAND_TEAM::ADD_REPOSITORY']);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function unsupportedAttributesProvider()
    {
        return [
            ['ROLE_USER'],
            ['CMD_'],
            ['TEAM::LEAVE'],
        ];
    }
}
