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

namespace Regis\Kernel\Infrastructure\Worker;

use Regis\Kernel\Worker\Message;
use Regis\Kernel\Worker\MessagePublisher;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Swarrot\Broker\Message as SwarrotMessage;

class SwarrotMessagePublisher implements MessagePublisher
{
    private $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public function scheduleInspection(array $message): void
    {
        $this->publisher->publish(Message::TYPE_ANALYSIS_INSPECTION, $this->message($message));
    }

    public function notifyInspectionOver(string $inspectionId, string $inspectionType): void
    {
        $this->publisher->publish(Message::TYPE_ANALYSIS_STATUS, $this->message([
            'inspection_id' => $inspectionId,
        ]), [
            'routing_key' => sprintf('analysis.%s.status', $inspectionType),
        ]);
    }

    private function message(array $data): SwarrotMessage
    {
        return new SwarrotMessage(json_encode($data));
    }
}
