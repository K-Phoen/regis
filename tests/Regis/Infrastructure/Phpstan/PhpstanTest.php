<?php

namespace Tests\Regis\Infrastructure\PhpMd;

use Regis\Infrastructure\Phpstan\Phpstan;

class PhpstanTest extends \PHPUnit_Framework_TestCase
{
    private $filename;

    public function setUp()
    {
        $source = <<<'CODE'
<?php

$foo = 42;
$f = function () use ($foo) {
};
CODE
        ;

        $this->filename = tempnam(sys_get_temp_dir(), 'regis_');
        file_put_contents($this->filename, $source);
    }

    public function tearDown()
    {
        unlink($this->filename);
    }

    public function testReportsAreGenerated()
    {
        $phpstan = new Phpstan(APP_ROOT_DIR.'/vendor/bin/phpstan');

        $this->assertSame([
            [
                'file' => $this->filename,
                'line' => 4,
                'column' => 1,
                'severity' => 'error',
                'message' => 'Anonymous function has an unused use $foo.',
            ],
        ], iterator_to_array($phpstan->execute($this->filename)));
    }
}
