<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Infrastructure\Github;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Entity\Repository;
use Regis\BitbucketContext\Infrastructure\Bitbucket\Client;
use Regis\BitbucketContext\Infrastructure\Bitbucket\ClientFactory;

class ClientFactoryTest extends TestCase
{
    /** @var ClientFactory */
    private $clientFactory;

    public function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);

        $this->clientFactory = new ClientFactory($logger);
    }

    public function testCreateForRepository()
    {
        $repository = $this->createMock(Repository::class);
        $owner = $this->createMock(BitbucketDetails::class);

        $repository->expects($this->once())
            ->method('getOwner')
            ->willReturn($owner);

        $client = $this->clientFactory->createForRepository($repository);

        $this->assertInstanceOf(Client::class, $client);
    }
}
