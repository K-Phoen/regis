<?php

namespace Tests\Regis\PhpMd;

use Regis\PhpMd\PhpMd;

class PhpMdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFilesDataProvider
     */
    public function testReportsAreGenerated(string $fileName, string $fileContent, array $expectedReports)
    {
        $phpMd = new PhpMd(APP_ROOT_DIR.'/vendor/bin/phpmd', [
            'rulesets' => ['codesize', 'unusedcode', 'naming', 'controversial', 'design', 'cleancode'],
        ]);

        $reports = iterator_to_array($phpMd->execute($fileName, $fileContent));
        $this->assertSame($expectedReports, $reports);
    }

    public function testFilesDataProvider()
    {
        list($violations, $fileContent) = $this->fileWithTwoViolations();

        return [
            ['test.php', $fileContent, $violations]
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
            ]
        ];

        return [$violations, $content];
    }
}
