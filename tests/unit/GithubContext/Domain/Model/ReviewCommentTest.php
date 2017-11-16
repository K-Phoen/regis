<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Domain\Model;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Model\ReviewComment;

class ReviewCommentTest extends TestCase
{
    public function testConstruction()
    {
        $comment = new ReviewComment('file.php', 2, 'comment content');

        $this->assertSame('file.php', $comment->getFile());
        $this->assertSame(2, $comment->getPosition());
        $this->assertSame('comment content', $comment->getContent());
    }
}
