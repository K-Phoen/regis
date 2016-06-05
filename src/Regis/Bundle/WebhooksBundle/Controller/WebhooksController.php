<?php

namespace Regis\Bundle\WebhooksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event;
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
        } catch (\Exception $e) {
            return new Response(sprintf("ignored:\n%s\n%s", $e->getMessage(), $e->getTraceAsString()));
        }

        $this->get('event_dispatcher')->dispatch($event->getEventName(), new DomainEventWrapper($event));

        return new Response('ok');
    }
}