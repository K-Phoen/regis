<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Infrastructure\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\User;
use Regis\AppContext\Infrastructure\Remote\AggregatedRepositories;
use Regis\AppContext\Infrastructure\Remote\Repositories;

class AggregateRepositoriesTest extends TestCase
{
    public function testItAggregatesResultsFromSeveralSources()
    {
        $user = new User();
        $source = $this->createMock(Repositories::class);
        $otherSource = $this->createMock(Repositories::class);

        $source->method('forUser')->willReturn(new \ArrayIterator(['foo']));
        $otherSource->method('forUser')->willReturn(new \ArrayIterator(['bar']));

        $results = (new AggregatedRepositories([$source, $otherSource]))->forUser($user);

        $this->assertSame(['foo', 'bar'], iterator_to_array($results, false));
    }
}
