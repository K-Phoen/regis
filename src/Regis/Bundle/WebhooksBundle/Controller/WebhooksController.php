<?php

namespace Regis\Bundle\WebhooksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event;

class WebhooksController extends Controller
{
    public function githubAction(Request $request)
    {
        try {
            $event = $this->get('regis.github.event_transformer')->transform($request);
        } catch (\Exception $e) {
            return new Response(sprintf("ignored:\n%s\n%s", $e->getMessage(), $e->getTraceAsString()));
        }

        $this->get('event_dispatcher')->dispatch($event->getEventName(), new DomainEventWrapper($event));

        return new Response('ok');
    }
}