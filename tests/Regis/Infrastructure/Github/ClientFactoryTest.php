<?php

namespace Tests\Regis\Infrastructure\Github;

use Github\Api;
use Github\Client as VendorClient;
use Psr\Log\LoggerInterface;

use Regis\Domain\Entity\Github\Repository;
use Regis\Domain\Entity\User;
use Regis\Infrastructure\Github\ClientFactory;

class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $vendorClient;
    private $logger;
    /** @var ClientFactory */
    private $clientFactory;

    public function setUp()
    {
        $this->vendorClient = $this->getMockBuilder(VendorClient::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->clientFactory = new ClientFactory($this->vendorClient, $this->logger);
    }

    public function testCreateForRepository()
    {
        $repository = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $owner = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $repository->expects($this->once())
            ->method('getOwner')
            ->will($this->returnValue($owner));

        $owner->expects($this->once())
            ->method('getGithubAccessToken')
            ->will($this->returnValue('access token'));

        $this->clientFactory->createForRepository($repository);
    }
}
