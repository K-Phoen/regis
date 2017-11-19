<?php

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
