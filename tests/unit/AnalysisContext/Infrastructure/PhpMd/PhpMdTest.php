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

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Infrastructure\PhpMd\PhpMd;

class PhpMdTest extends TestCase
{
    const RULESETS = ['codesize', 'unusedcode', 'naming', 'controversial', 'design', 'cleancode'];

    /**
     * @dataProvider filesDataProvider
     */
    public function testReportsAreGenerated(string $fileName, string $fileContent, array $expectedReports)
    {
        $phpMd = new PhpMd(APP_ROOT_DIR.'/vendor/bin/phpmd');

        $reports = iterator_to_array($phpMd->execute($fileName, $fileContent, implode(',', self::RULESETS)));
        $this->assertSame($expectedReports, $reports);
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

class Foo {
    public function bar() {
        $variableNameObviouslyTooLongToBeUseful = "foo";
    }
}
';

        $violations = [
            [
                'file' => 'test.php',
                'beginLine' => 5,
                'endLine' => 5,
                'rule' => 'UnusedLocalVariable',
                'ruleSet' => 'Unused Code Rules',
                'externalInfoUrl' => 'http://phpmd.org/rules/unusedcode.html#unusedlocalvariable',
                'priority' => 3,
                'description' => 'Avoid unused local variables such as \'$variableNameObviouslyTooLongToBeUseful\'.',
            ],
            [
                'file' => 'test.php',
                'beginLine' => 5,
                'endLine' => 5,
                'rule' => 'LongVariable',
                'ruleSet' => 'Naming Rules',
                'externalInfoUrl' => 'http://phpmd.org/rules/naming.html#longvariable',
                'priority' => 3,
                'description' => 'Avoid excessively long variable names like $variableNameObviouslyTooLongToBeUseful. Keep variable name length under 20.',
            ],
        ];

        return [$violations, $content];
    }
}
