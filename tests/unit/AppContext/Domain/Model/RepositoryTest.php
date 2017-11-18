<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Domain\Model;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Model\Repository;

class RepositoryTest extends TestCase
{
    public function testItHoldsData()
    {
        $repo = new Repository('repo-identifier', 'repo-name', 'public-url', 'repo-type');

        $this->assertSame('repo-identifier', $repo->getIdentifier());
        $this->assertSame('repo-name', $repo->getName());
        $this->assertSame('public-url', $repo->getPublicUrl());
        $this->assertSame('repo-type', $repo->getType());
    }

    public function testItCanBeConvertedToAnArray()
    {
        $repo = new Repository('repo-identifier', 'repo-name', 'public-url', 'repo-type');

        $this->assertSame([
            'identifier' => 'repo-identifier',
            'name' => 'repo-name',
            'public_url' => 'public-url',
            'type' => 'repo-type',
        ], $repo->toArray());
    }
}
