<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\Repository;

class RepositoryTest extends TestCase
{
    const OWNER_NAME = 'K-Phoen';

    public function testItCanChangeTheSecret()
    {
        $repository = new Repository();

        $repository->newSharedSecret('some secret');

        $this->assertSame('some secret', $repository->getSharedSecret());

        $repository->newSharedSecret('new secret');

        $this->assertSame('new secret', $repository->getSharedSecret());
    }
}
