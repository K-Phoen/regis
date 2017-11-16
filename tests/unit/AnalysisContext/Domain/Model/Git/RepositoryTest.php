<?php

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

        $this->assertEquals('clone url', $repository->getCloneUrl());
        $this->assertEquals('K-Phoen/test', $repository->getIdentifier());
        $this->assertEquals('K-Phoen/test', (string) $repository);
    }

    public function testItCanBeTransformedToAnArray()
    {
        $repository = new Git\Repository('K-Phoen/test', 'clone url');

        $this->assertEquals([
            'identifier' => 'K-Phoen/test',
            'clone_url' => 'clone url',
        ], $repository->toArray());
    }
}
