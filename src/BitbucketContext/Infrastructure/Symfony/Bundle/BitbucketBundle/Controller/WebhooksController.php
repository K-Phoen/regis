<?php

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
