<?php

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\BitbucketContext\Domain\Model\Repository;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

class PullRequestTest extends TestCase
{
    public function testItHoldsData()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $pr = new PullRequest($repoIdentifier, 42, 'head sha', 'base sha');

        $this->assertSame($repoIdentifier, $pr->getRepository());
        $this->assertSame(42, $pr->getNumber());
        $this->assertSame('head sha', $pr->getHead());
        $this->assertSame('base sha', $pr->getBase());
    }

    public function testItCanBeConvertedToAString()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $pr = new PullRequest($repoIdentifier, 42, 'head sha', 'base sha');

        $this->assertSame('identifier-value#42', (string) $pr);
    }

    public function testItCanBeConvertedToAnArray()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $pr = new PullRequest($repoIdentifier, 42, 'head sha', 'base sha');

        $this->assertSame([
            'repository_identifier' => ['identifier' => 'identifier-value'],
            'number' => 42,
            'head' => 'head sha',
            'base' => 'base sha',
        ], $pr->toArray());
    }

    public function testItCanBeCreatedFromAnArray()
    {
        $pr = PullRequest::fromArray([
            'repository_identifier' => ['identifier' => 'identifier-value'],
            'number' => 42,
            'head' => 'head sha',
            'base' => 'base sha',
        ]);

        $this->assertSame('identifier-value', $pr->getRepository()->value());
        $this->assertSame(42, $pr->getNumber());
        $this->assertSame('head sha', $pr->getHead());
        $this->assertSame('base sha', $pr->getBase());
    }
}
