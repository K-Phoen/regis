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

namespace Tests\Regis\BitbucketContext\Domain\Model;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Entity\Violation;
use Regis\BitbucketContext\Domain\Model\ReviewComment;
use Tests\Regis\Helper\ObjectManipulationHelper;

class ReviewCommentTest extends TestCase
{
    use ObjectManipulationHelper;

    public function testConstruction()
    {
        $comment = new ReviewComment('file.php', 2, 'comment content');

        $this->assertSame('file.php', $comment->file());
        $this->assertSame(2, $comment->line());
        $this->assertSame('comment content', $comment->content());
    }

    public function testConstructionFromAViolation()
    {
        $violation = new Violation();
        $this->setPrivateValue($violation, 'line', 2);
        $this->setPrivateValue($violation, 'file', 'file.php');
        $this->setPrivateValue($violation, 'description', 'comment content');

        $comment = ReviewComment::fromViolation($violation);

        $this->assertSame('file.php', $comment->file());
        $this->assertSame(2, $comment->line());
        $this->assertSame('comment content', $comment->content());
    }
}
