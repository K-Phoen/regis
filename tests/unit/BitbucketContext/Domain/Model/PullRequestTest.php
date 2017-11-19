<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Model\PullRequest;
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
