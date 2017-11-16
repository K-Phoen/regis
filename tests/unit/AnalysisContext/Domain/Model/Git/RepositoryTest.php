<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Domain\Model\Git;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class RepositoryTest extends TestCase
{
    public function testItCanBeConstructedFromAnArray()
    {
        $repository = Git\Repository::fromArray([
            'identifier' => 'K-Phoen/test',
            'clone_url' => 'clone url',
        ]);

        $this->assertSame('clone url', $repository->getCloneUrl());
        $this->assertSame('K-Phoen/test', $repository->getIdentifier());
        $this->assertSame('K-Phoen/test', (string) $repository);
    }

    public function testItCanBeTransformedToAnArray()
    {
        $repository = new Git\Repository('K-Phoen/test', 'clone url');

        $this->assertSame([
            'identifier' => 'K-Phoen/test',
            'clone_url' => 'clone url',
        ], $repository->toArray());
    }
}
