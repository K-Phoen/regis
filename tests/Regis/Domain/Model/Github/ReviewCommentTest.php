<?php

namespace Tests\Regis\Domain\Model\Github;

use PHPUnit\Framework\TestCase;
use Regis\Domain\Model\Github\ReviewComment;

class ReviewCommentTest extends TestCase
{
    public function testConstruction()
    {
        $comment = new ReviewComment('file.php', 2, 'comment content');

        $this->assertEquals('file.php', $comment->getFile());
        $this->assertEquals(2, $comment->getPosition());
        $this->assertEquals('comment content', $comment->getContent());
    }
}
