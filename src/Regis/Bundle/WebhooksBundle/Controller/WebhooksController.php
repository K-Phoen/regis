<?php

namespace Regis\Bundle\WebhooksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event;
use Regis\Github\Exception\EventNotHandled;
use Regis\Github\Exception\PayloadSignature;

class WebhooksController extends Controller
{
    public function githubAction(Request $request)
    {
        try {
            $this->get('regis.github.payload_validator')->validate($request);
        } catch (PayloadSignature $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e);
        }

        try {
            $event = $this->get('regis.github.event_transformer')->transform($request);
            $this->info('Received payload of type {type}', [
                'type' => $request->headers->get('X-GitHub-Event'),
                'analysed_type' => $event->getEventName(),
                'payload_id' => $request->headers->get('X-GitHub-Delivery'),
            ]);
        } catch (EventNotHandled $e) {
            $this->info('Ignored payload of type {type}', [
                'type' => $request->headers->get('X-GitHub-Event'),
                'payload_id' => $request->headers->get('X-GitHub-Delivery'),
                'explanation' => $e->getMessage()
            ]);

            return new Response();
        }

        $this->get('event_dispatcher')->dispatch($event->getEventName(), new DomainEventWrapper($event));

        return new Response('', Response::HTTP_ACCEPTED);
    }

    private function info(string $message, array $context = [])
    {
        $this->get('monolog.logger.github')->info($message, $context);
    }
}