<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

class RepositoryIdentifierTest extends TestCase
{
    public function testItHoldsData()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');

        $this->assertSame('identifier-value', $repoIdentifier->value());
    }

    public function testItCanBeConvertedToAString()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');

        $this->assertSame('identifier-value', (string) $repoIdentifier);
    }

    public function testItCanBeConvertedToAnArray()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');

        $this->assertSame(['identifier' => 'identifier-value'], $repoIdentifier->toArray());
    }

    public function testItCanBeCreatedFromAnArray()
    {
        $repoIdentifier = RepositoryIdentifier::fromArray(['identifier' => 'identifier-value']);

        $this->assertSame('identifier-value', $repoIdentifier->value());
    }
}
