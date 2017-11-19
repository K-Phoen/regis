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

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\Controller;

use Regis\BitbucketContext\Application\Bitbucket\Exception\EventNotHandled;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhooksController extends Controller
{
    public function webhookAction(Request $request)
    {
        // TODO check that the repository is known
        try {
            $event = $this->get('regis.bitbucket.event_transformer')->transform($request);
            $this->info('Received payload of type {type}', [
                'type' => $request->headers->get('X-Event-Key'),
                'domain_event_type' => get_class($event),
                'payload_id' => $request->headers->get('X-Hook-UUID'),
            ]);
        } catch (EventNotHandled $e) {
            $this->info('Ignored payload of type {type}', [
                'type' => $request->headers->get('X-Event-Key'),
                'payload_id' => $request->headers->get('X-Hook-UUID'),
                'explanation' => $e->getMessage(),
            ]);

            return new Response();
        }

        $this->get('event_dispatcher')->dispatch(get_class($event), new DomainEventWrapper($event));

        return new Response('', Response::HTTP_ACCEPTED);
    }

    private function info(string $message, array $context = [])
    {
        $this->get('monolog.logger.bitbucket')->info($message, $context);
    }
}
