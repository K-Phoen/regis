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

use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Infrastructure\Phpstan\Phpstan;
use Tests\Regis\AnalysisContext\Infrastructure\InspectionRunnerTestCase;

class PhpstanTest extends InspectionRunnerTestCase
{
    /** @var Phpstan */
    private $phpstan;

    protected function files(): array
    {
        return [
            'test.php' => <<<'CODE'
<?php

$foo = 42;
$f = function () use ($foo) {
};
CODE
            ,
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->phpstan = new Phpstan($this->processRunner(), APP_ROOT_DIR.'/vendor/bin/phpstan');
    }

    public function testReportsAreGenerated()
    {
        $this->assertSame([
            [
                'file' => $this->path('test.php'),
                'line' => 4,
                'column' => 1,
                'severity' => 'error',
                'message' => 'Anonymous function has an unused use $foo.',
            ],
        ], iterator_to_array($this->phpstan->execute(new Env($this->workingDir()), $this->path('test.php'), null)));
    }
}
