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

use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Infrastructure\CodeSniffer\CodeSniffer;
use Tests\Regis\AnalysisContext\Infrastructure\InspectionRunnerTestCase;

class CodeSnifferTest extends InspectionRunnerTestCase
{
    const STANDARDS = 'psr1,psr2';

    /** @var CodeSniffer */
    private $codesniffer;

    protected function files(): array
    {
        return [
            'test.php' => <<<'CODE'
<?php

if(true) {
echo "Coucou";
}

CODE
            ,
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->codesniffer = new CodeSniffer($this->processRunner(), APP_ROOT_DIR.'/vendor/bin/phpcs');
    }

    /**
     * @dataProvider filesDataProvider
     */
    public function testReportsAreGenerated(string $fileName, array $expectedReports)
    {
        $env = new Env($this->workingDir());

        $this->assertSame($expectedReports, $this->codesniffer->execute($env, $this->path($fileName), self::STANDARDS));
    }

    public function filesDataProvider()
    {
        $testViolations = [
            'totals' => [
                'errors' => 2,
                'warnings' => 0,
                'fixable' => 2,
            ],
            'files' => [
                $this->path('test.php') => [
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

        return [
            ['test.php', $testViolations],
        ];
    }
}
