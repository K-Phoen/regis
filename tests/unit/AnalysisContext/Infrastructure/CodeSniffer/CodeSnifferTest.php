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

namespace Tests\Regis\AnalysisContext\Infrastructure\CodeSniffer;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Infrastructure\CodeSniffer\CodeSniffer;

class CodeSnifferTest extends TestCase
{
    const STANDARDS = 'psr1,psr2';

    /**
     * @dataProvider filesDataProvider
     */
    public function testReportsAreGenerated(string $fileName, string $fileContent, array $expectedReports)
    {
        $phpcs = new CodeSniffer(APP_ROOT_DIR.'/vendor/bin/phpcs');

        $this->assertSame($expectedReports, $phpcs->execute($fileName, $fileContent, self::STANDARDS));
    }

    public function filesDataProvider()
    {
        list($violations, $fileContent) = $this->fileWithTwoViolations();

        return [
            ['test.php', $fileContent, $violations],
        ];
    }

    private function fileWithTwoViolations()
    {
        $content = '<?php

if(true) {
echo "Coucou";
}
';

        $violations = [
            'totals' => [
                'errors' => 2,
                'warnings' => 0,
                'fixable' => 2,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 2,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'Expected 1 space after IF keyword; 0 found',
                            'source' => 'Squiz.ControlStructures.ControlSignature.SpaceAfterKeyword',
                            'severity' => 5,
                            'type' => 'ERROR',
                            'line' => 3,
                            'column' => 1,
                            'fixable' => true,
                        ],
                        [
                            'message' => 'Line indented incorrectly; expected at least 4 spaces, found 0',
                            'source' => 'Generic.WhiteSpace.ScopeIndent.Incorrect',
                            'severity' => 5,
                            'type' => 'ERROR',
                            'line' => 4,
                            'column' => 1,
                            'fixable' => true,
                        ],
                    ],
                ],
            ],
        ];

        return [$violations, $content];
    }
}
