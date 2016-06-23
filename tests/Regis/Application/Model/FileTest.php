<?php

namespace Tests\Regis\Application\Model;

use Regis\Application\Model\Git\Blob;
use Regis\Application\Model\Git\Diff\File;
use Tests\Git\DiffParser;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testDeletionsAreDetected()
    {
        $diffFile = new File('old-name.php', null, false, $blob = $this->getBlob(), []);

        $this->assertTrue($diffFile->isDeletion());
        $this->assertFalse($diffFile->isRename());
        $this->assertFalse($diffFile->isModification());
        $this->assertFalse($diffFile->isCreation());
        $this->assertSame('old-name.php', $diffFile->getOldName());
        $this->assertNull($diffFile->getNewName());
        $this->assertSame($blob, $diffFile->getNewBlob());
        $this->assertFalse($diffFile->isBinary());
    }

    public function testRenamesAreDetected()
    {
        $diffFile = new File('old-name.php', 'new-name.php', false, $this->getBlob(), []);

        $this->assertFalse($diffFile->isDeletion());
        $this->assertTrue($diffFile->isRename());
        $this->assertTrue($diffFile->isModification());
        $this->assertFalse($diffFile->isCreation());
    }

    public function testACreationIsNotARename()
    {
        $diffFile = new File(null, 'new-name.php', false, $this->getBlob(), []);

        $this->assertFalse($diffFile->isDeletion());
        $this->assertFalse($diffFile->isRename());
        $this->assertFalse($diffFile->isModification());
        $this->assertTrue($diffFile->isCreation());
    }

    /**
     * @expectedException \Regis\Application\Model\Exception\LineNotInDiff
     */
    public function testFindPositionForLineFailsIfTheLineIsNotInTheDiff()
    {
        $diffFile = new File(null, 'new-name.php', false, $this->getBlob(), []);

        $diffFile->findPositionForLine(42);
    }

    /**
     * @dataProvider positionProvider
     */
    public function testFindPositionForLine(string $diffContent, int $line, int $expectedPosition)
    {
        $files = (new DiffParser())->parse($diffContent);

        $this->assertEquals($expectedPosition, $files[0]->findPositionForLine($line));
    }

    public function positionProvider(): array
    {
        $diff1 = 'diff --git a/src/Regis/Bundle/WebhooksBundle/Worker/WebhookEvent.php b/src/Regis/Bundle/WebhooksBundle/Worker/WebhookEvent.php
index eed2c89..d62fdc2 100644
--- a/src/Regis/Bundle/WebhooksBundle/Worker/WebhookEvent.php
+++ b/src/Regis/Bundle/WebhooksBundle/Worker/WebhookEvent.php
@@ -32,9 +32,14 @@ public function execute(AMQPMessage $msg)
 
             $this->dispatch(Event::INSPECTION_STARTED, new Event\InspectionStarted($pullRequest));
 
-            $reportSummary = $this->inspector->inspect($pullRequest);
+            try {
+                $reportSummary = $this->inspector->inspect($pullRequest);
+                $this->dispatch(Event::INSPECTION_FINISHED, new Event\InspectionFinished($pullRequest, $reportSummary));
+            } catch (\Exception $e) {
+                $this->dispatch(Event::INSPECTION_FAILED, new Event\InspectionFailed($pullRequest, $e));
+                throw $e;
+            }
 
-            $this->dispatch(Event::INSPECTION_FINISHED, new Event\InspectionFinished($pullRequest, $reportSummary));
         }
     }
';

        $diff2 = 'diff --git a/src/Regis/Application/Event.php b/src/Regis/Application/Event.php
index 1c4206f..159c0a1 100644
--- a/src/Regis/Application/Event.php
+++ b/src/Regis/Application/Event.php
@@ -12,6 +12,7 @@
 
     const INSPECTION_STARTED = \'inspection_started\';
     const INSPECTION_FINISHED = \'inspection_finished\';
+    const INSPECTION_FAILED = \'inspection_failed\';
 
     function getEventName(): string;
 }
\ No newline at end of file
';

        $diff3 = 'diff --git a/src/Regis/Bundle/WebhooksBundle/EventListener/PullRequestInspectionStatusListener.php b/src/Regis/Bundle/WebhooksBundle/EventListener/PullRequestInspectionStatusListener.php
index 88ab614..48ca702 100644
--- a/src/Regis/Bundle/WebhooksBundle/EventListener/PullRequestInspectionStatusListener.php
+++ b/src/Regis/Bundle/WebhooksBundle/EventListener/PullRequestInspectionStatusListener.php
@@ -27,8 +27,10 @@ public static function getSubscribedEvents()
         return [
             Event::PULL_REQUEST_OPENED => \'onPullRequestUpdated\',
             Event::PULL_REQUEST_SYNCED => \'onPullRequestUpdated\',
+
             Event::INSPECTION_STARTED => \'onInspectionStated\',
             Event::INSPECTION_FINISHED => \'onInspectionFinished\',
+            Event::INSPECTION_FAILED => \'onInspectionFailed\',
         ];
     }
 
@@ -63,6 +65,14 @@ public function onInspectionFinished(DomainEventWrapper $event)
         $this->setIntegrationStatus($domainEvent->getPullRequest(), $status, $message);
     }
 
+    public function onInspectionFailed(DomainEventWrapper $event)
+    {
+        /** @var Event\InspectionFailed $domainEvent */
+        $domainEvent = $event->getDomainEvent();
+
+        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_FAILURE, \'Inspection failed.\');
+    }
+
     private function setIntegrationStatus(PullRequest $pullRequest, string $status, string $description)
     {
         $this->github->setIntegrationStatus($pullRequest, $status, $description, self::STATUS_CONTEXT);
';

        $diff4 = 'diff --git a/src/Regis/Application/Model/Git/Diff/File.php b/src/Regis/Application/Model/Git/Diff/File.php
index 76fe5e2..e2499d5 100644
--- a/src/Regis/Application/Model/Git/Diff/File.php
+++ b/src/Regis/Application/Model/Git/Diff/File.php
@@ -4,6 +4,7 @@
 
 namespace Regis\Application\Model\Git\Diff;
 
+use Regis\Application\Model\Exception\LineNotInDiff;
 use Regis\Application\Model\Git\Blob;
 
 class File
@@ -66,4 +67,26 @@ public function getChanges(): array
     {
         return $this->changes;
     }
+
+    public function findPositionForLine(int $line): int
+    {
+        $changes = $this->getChanges();
+
+        $previousChangeCount = 0;
+        /** @var Change $change */
+        foreach ($changes as $change) {
+            $rangeStart = $change->getRangeNewStart() - 1;
+
+            /** @var Line $diffLine */
+            foreach ($change->getAddedLines() as $diffLine) {
+                if ($rangeStart + $diffLine->getPosition() === $line) {
+                    return $previousChangeCount + $diffLine->getPosition();
+                }
+            }
+
+            $previousChangeCount = $change->getRangeNewCount() + 1;
+        }
+
+        throw LineNotInDiff::line($line);
+    }
 }
\ No newline at end of file
';

        $diff5 = 'diff --git a/src/Regis/Application/Reporter/DuplicationGuard.php b/src/Regis/Application/Reporter/DuplicationGuard.php
index a5cf1fb..64c6816 100644
--- a/src/Regis/Application/Reporter/DuplicationGuard.php
+++ b/src/Regis/Application/Reporter/DuplicationGuard.php
@@ -21,12 +21,13 @@ public function __construct(Reporter $originalReporter, ViolationsCache $violati
 
     public function report(Model\Violation $violation, Model\Github\PullRequest $pullRequest)
     {
-        if ($this->violationsCache->has($violation, $pullRequest)) {
+        if($this->violationsCache->has($violation, $pullRequest)) {
             return;
         }
 
         $this->originalReporter->report($violation, $pullRequest);
 
         $this->violationsCache->save($violation, $pullRequest);
+
     }
-}
\ No newline at end of file
+}
';

        return [
            [ $diff1, 42, 12 ],
            [ $diff2, 15, 4 ],
            [ $diff3, 30, 4 ],
            [ $diff3, 33, 7 ],
            [ $diff4, 7, 4 ],
            [ $diff4, 90, 32 ],
            [ $diff5, 24, 5 ],
        ];
    }

    private function getBlob(): Blob
    {
        return $this->getMockBuilder(Blob::class)->disableOriginalConstructor()->getMock();
    }
}
