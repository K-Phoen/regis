<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Infrastructure\CodeSniffer;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Infrastructure\CodeSniffer\CodeSniffer;

class CodeSnifferTest extends TestCase
{
    /**
     * @dataProvider filesDataProvider
     */
    public function testReportsAreGenerated(string $fileName, string $fileContent, array $expectedReports)
    {
        $phpcs = new CodeSniffer(APP_ROOT_DIR.'/vendor/bin/phpcs', [
            'options' => ['--standard=psr1,psr2'],
        ]);

        $this->assertSame($expectedReports, $phpcs->execute($fileName, $fileContent));
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
