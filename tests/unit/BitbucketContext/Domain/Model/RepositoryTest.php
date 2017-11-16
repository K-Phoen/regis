<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Model\Repository;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

class RepositoryTest extends TestCase
{
    public function testItHoldsData()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $repo = new Repository($repoIdentifier, 'repo-name', 'clone-url', 'public-url');

        $this->assertSame($repoIdentifier, $repo->getIdentifier());
        $this->assertSame('repo-name', $repo->getName());
        $this->assertSame('clone-url', $repo->getCloneUrl());
        $this->assertSame('public-url', $repo->getPublicUrl());
    }

    public function testItCanBeConvertedToAString()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $repo = new Repository($repoIdentifier, 'repo-name', 'clone-url', 'public-url');

        $this->assertSame('identifier-value', (string) $repo);
    }

    public function testItCanBeConvertedToAnArray()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $repo = new Repository($repoIdentifier, 'repo-name', 'clone-url', 'public-url');

        $this->assertSame([
            'identifier' => ['identifier' => 'identifier-value'],
            'name' => 'repo-name',
            'clone_url' => 'clone-url',
            'public_url' => 'public-url',
        ], $repo->toArray());
    }

    public function testItCanBeCreatedFromAnArray()
    {
        $repo = Repository::fromArray([
            'identifier' => ['identifier' => 'identifier-value'],
            'name' => 'repo-name',
            'clone_url' => 'clone-url',
            'public_url' => 'public-url',
        ]);

        $this->assertSame('identifier-value', $repo->getIdentifier()->value());
        $this->assertSame('repo-name', $repo->getName());
        $this->assertSame('clone-url', $repo->getCloneUrl());
        $this->assertSame('public-url', $repo->getPublicUrl());
    }
}
