<?php

namespace Tests\Regis\Kernel\Bundle\AuthBundle\Security;

use PHPUnit\Framework\TestCase;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Regis\GithubContext\Domain\Entity;
use Regis\Kernel\Bundle\AuthBundle\Security\CommandVoter;

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

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testItDeniesTheAccessIfTheCommandDoesNotExist()
    {
        $subject = 'does not matter';

        $result = $this->voter->vote($this->token, $subject, ['COMMAND_THAT_DOES_NOT_EXIST']);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testItAllowsTheAccessIfTheCommandIsNotSecure()
    {
        $subject = 'does not matter';

        $result = $this->voter->vote($this->token, $subject, ['COMMAND_TEAM::CREATE']);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
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

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
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
