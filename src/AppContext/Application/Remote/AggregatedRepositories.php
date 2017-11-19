<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Remote;

use Regis\Kernel;

class AggregatedRepositories implements Repositories
{
    private $sources;

    /**
     * @param Repositories[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function forUser(Kernel\User $user): \Traversable
    {
        foreach ($this->sources as $source) {
            yield from $source->forUser($user);
        }
    }
}
