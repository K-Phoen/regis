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

namespace Tests\Regis\AnalysisContext\Infrastructure\Phpstan;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Infrastructure\Phpstan\Phpstan;
use Regis\AnalysisContext\Infrastructure\Process\SymfonyProcessRunner;

class PhpstanTest extends TestCase
{
    private $workingDir;
    private $phpstan;
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

        $this->workingDir = sys_get_temp_dir();
        $this->filename = tempnam($this->workingDir, 'regis_');
        file_put_contents($this->filename, $source);

        $logger = $this->createMock(LoggerInterface::class);
        $this->phpstan = new Phpstan(new SymfonyProcessRunner($logger), APP_ROOT_DIR.'/vendor/bin/phpstan');
    }

    public function tearDown()
    {
        unlink($this->filename);
    }

    public function testReportsAreGenerated()
    {
        $this->assertSame([
            [
                'file' => $this->filename,
                'line' => 4,
                'column' => 1,
                'severity' => 'error',
                'message' => 'Anonymous function has an unused use $foo.',
            ],
        ], iterator_to_array($this->phpstan->execute(new Env($this->workingDir), $this->filename, null)));
    }
}
