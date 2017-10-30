<?php

namespace Tests\Regis\Infrastructure\Github;

use PHPUnit\Framework\TestCase;
use Github\Client as VendorClient;
use Psr\Log\LoggerInterface;

use Regis\Domain\Entity\Github\Repository;
use Regis\Domain\Entity\User;
use Regis\Infrastructure\Github\Client;
use Regis\Infrastructure\Github\ClientFactory;

class ClientFactoryTest extends TestCase
{
    private $vendorClient;
    private $logger;
    /** @var ClientFactory */
    private $clientFactory;

    public function setUp()
    {
        $this->vendorClient = $this->createMock(VendorClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->clientFactory = new ClientFactory($this->vendorClient, $this->logger);
    }

    public function testCreateForRepository()
    {
        $repository = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $owner = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $repository->expects($this->once())
            ->method('getOwner')
            ->will($this->returnValue($owner));

        $client = $this->clientFactory->createForRepository($repository);

        $this->assertInstanceOf(Client::class, $client);
    }
}
