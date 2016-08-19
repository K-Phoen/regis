<?php

declare(strict_types=1);

/**
 * This file was part of Gitonomy and has been modified to suit our needs.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 * (c) Kévin Gomez <contact@kevingomez.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Regis\Domain\Git;

use Gitonomy\Git\Parser\ParserBase;

use Regis\Domain\Model\Git\Blob;
use Regis\Domain\Model\Git\Diff;

/**
 * TODO write tests, rewrite
 */
class DiffParser extends ParserBase
{
    private $files = [];

    public function parse($content): array
    {
        parent::parse($content);

        return $this->files;
    }

    protected function doParse()
    {
        $this->files = [];

        while (!$this->isFinished()) {
            // 1. title
            $vars = $this->consumeRegexp('/diff --git (a\/.*) (b\/.*)\n/');
            $oldName = $vars[1];
            $newName = $vars[2];
            $oldIndex = null;
            $newIndex = null;
            $oldMode = null;
            $newMode = null;

            // 2. mode
            if ($this->expects('new file mode ')) {
                $newMode = $this->consumeTo("\n");
                $this->consumeNewLine();
                $oldMode = null;
            }
            if ($this->expects('old mode ')) {
                $oldMode = $this->consumeTo("\n");
                $this->consumeNewLine();
                $this->consume('new mode ');
                $newMode = $this->consumeTo("\n");
                $this->consumeNewLine();
            }
            if ($this->expects('deleted file mode ')) {
                $oldMode = $this->consumeTo("\n");
                $newMode = null;
                $this->consumeNewLine();
            }

            if ($this->expects('similarity index ')) {
                $this->consumeRegexp('/\d{1,3}%\n/');
                $this->consume('rename from ');
                $this->consumeTo("\n");
                $this->consumeNewLine();
                $this->consume('rename to ');
                $this->consumeTo("\n");
                $this->consumeNewLine();
            }

            // 4. File informations
            $isBinary = false;
            if ($this->expects('index ')) {
                $oldIndex = $this->consumeShortHash();
                $this->consume('..');
                $newIndex = $this->consumeShortHash();
                if ($this->expects(' ')) {
                    $vars = $this->consumeRegexp('/\d{6}/');
                    $newMode = $oldMode = $vars[0];
                }
                $this->consumeNewLine();

                if ($this->expects('--- ')) {
                    $oldName = $this->consumeTo("\n");
                    $this->consumeNewLine();
                    $this->consume('+++ ');
                    $newName = $this->consumeTo("\n");
                    $this->consumeNewLine();
                } elseif ($this->expects('Binary files ')) {
                    $vars = $this->consumeRegexp('/(.*) and (.*) differ\n/');
                    $isBinary = true;
                    $oldName = $vars[1];
                    $newName = $vars[2];
                }
            }

            $oldName = $oldName === '/dev/null' ? null : substr($oldName, 2);
            $newName = $newName === '/dev/null' ? null : substr($newName, 2);
            $oldIndex = preg_match('/^0+$/', $oldIndex) ? null : $oldIndex;
            $newIndex = preg_match('/^0+$/', $newIndex) ? null : $newIndex;

            // 5. Diff
            $changes = [];
            while ($this->expects('@@ ')) {
                $vars = $this->consumeRegexp('/-(\d+)(?:,(\d+))? \+(\d+)(?:,(\d+))?/');
                $rangeOldStart = (int) $vars[1];
                $rangeOldCount = (int) $vars[2];
                $rangeNewStart = (int) $vars[3];
                $rangeNewCount = (int) $vars[4] ?? $vars[2]; // @todo Ici, t'as pris un gros raccourci mon loulou
                $this->consume(' @@');
                $this->consumeTo("\n");
                $this->consumeNewLine();

                // 6. Lines
                $lines = [];
                $position = 1;
                $currentLine = $rangeNewStart;
                while (true) {
                    if ($this->expects(' ')) {
                        $lines[] = new Diff\Line(Diff\Change::LINE_CONTEXT, $position, $currentLine, $this->consumeTo("\n"));
                        $currentLine++;
                    } elseif ($this->expects('+')) {
                        $lines[] = new Diff\Line(Diff\Change::LINE_ADD, $position, $currentLine, $this->consumeTo("\n"));
                        $currentLine++;
                    } elseif ($this->expects('-')) {
                        $lines[] = new Diff\Line(Diff\Change::LINE_REMOVE, $position, $currentLine, $this->consumeTo("\n"));
                    } elseif ($this->expects("\ No newline at end of file")) {
                        // Ignore this case...
                    } else {
                        break;
                    }

                    $this->consumeNewLine();
                    $position++;
                }

                $changes[] = new Diff\Change($rangeOldStart, $rangeOldCount, $rangeNewStart, $rangeNewCount, $lines);
            }

            $this->files[] = new Diff\File($oldName, $newName, $isBinary, $this->getEmptyBlob(), $changes);
        }
    }

    private function getEmptyBlob(): Blob
    {
        return new Blob('dummy_hash', 'dummy content', 'text/plain');
    }
}
