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

namespace Tests\Regis\AnalysisContext\Infrastructure\PhpMd;

use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Infrastructure\PhpMd\PhpMd;
use Tests\Regis\AnalysisContext\Infrastructure\InspectionRunnerTestCase;

class PhpMdTest extends InspectionRunnerTestCase
{
    const RULESETS = ['codesize', 'unusedcode', 'naming', 'controversial', 'design', 'cleancode'];

    /** @var PhpMd */
    private $phpmd;

    protected function files(): array
    {
        return [
            'test.php' => <<<'CODE'
<?php

class Foo {
    public function bar() {
        $variableNameObviouslyTooLongToBeUseful = "foo";
    }
}
CODE
            ,
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->phpmd = new PhpMd($this->processRunner(), APP_ROOT_DIR.'/vendor/bin/phpmd');
    }

    /**
     * @dataProvider filesDataProvider
     */
    public function testReportsAreGenerated(string $fileName, array $expectedReports)
    {
        $env = new Env($this->workingDir());

        $reports = iterator_to_array($this->phpmd->execute($env, $this->path($fileName), implode(',', self::RULESETS)));
        $this->assertSame($expectedReports, $reports);
    }

    public function filesDataProvider()
    {
        $testViolations = [
            [
                'file' => $this->path('test.php'),
                'beginLine' => 5,
                'endLine' => 5,
                'rule' => 'UnusedLocalVariable',
                'ruleSet' => 'Unused Code Rules',
                'externalInfoUrl' => 'http://phpmd.org/rules/unusedcode.html#unusedlocalvariable',
                'priority' => 3,
                'description' => 'Avoid unused local variables such as \'$variableNameObviouslyTooLongToBeUseful\'.',
            ],
            [
                'file' => $this->path('test.php'),
                'beginLine' => 5,
                'endLine' => 5,
                'rule' => 'LongVariable',
                'ruleSet' => 'Naming Rules',
                'externalInfoUrl' => 'http://phpmd.org/rules/naming.html#longvariable',
                'priority' => 3,
                'description' => 'Avoid excessively long variable names like $variableNameObviouslyTooLongToBeUseful. Keep variable name length under 20.',
            ],
        ];

        return [
            ['test.php', $testViolations],
        ];
    }
}
