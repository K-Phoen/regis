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

namespace Tests\Regis\AnalysisContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Entity\Inspection;
use Regis\AnalysisContext\Domain\Entity\Report;

class InspectionTest extends TestCase
{
    public function testStart()
    {
        $inspection = new Inspection();

        $this->assertNull($inspection->startedAt());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->status());

        $inspection->start();

        $this->assertNotNull($inspection->startedAt());
        $this->assertSame(Inspection::STATUS_STARTED, $inspection->status());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage This inspection is already started
     */
    public function testStartCanBeCalledOnlyOnce()
    {
        $inspection = new Inspection();

        $inspection->start();
        $inspection->start();
    }

    public function testFinish()
    {
        $report = new Report('raw diff');
        $inspection = new Inspection();

        $this->assertNull($inspection->finishedAt());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->status());

        $inspection->finish($report);

        $this->assertNotNull($inspection->finishedAt());
        $this->assertSame(Inspection::STATUS_FINISHED, $inspection->status());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage This inspection is already finished
     */
    public function testFinishCanBeCalledOnlyOnce()
    {
        $report = new Report('raw diff');
        $inspection = new Inspection();

        $inspection->finish($report);
        $inspection->finish($report);
    }

    public function testFail()
    {
        $inspection = new Inspection();

        $this->assertNull($inspection->finishedAt());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->status());

        $inspection->fail(new \Exception('Message'));

        $this->assertNotNull($inspection->finishedAt());
        $this->assertSame(Inspection::STATUS_FAILED, $inspection->status());
        $this->assertNotEmpty($inspection->failureTrace());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage This inspection is already finished
     */
    public function testFailhCanBeCalledOnlyOnce()
    {
        $inspection = new Inspection();

        $inspection->fail(new \Exception());
        $inspection->fail(new \Exception());
    }
}
