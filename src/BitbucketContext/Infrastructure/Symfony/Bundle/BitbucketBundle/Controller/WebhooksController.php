<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhooksController extends Controller
{
    public function webhookAction(Request $request)
    {
        return new Response('', Response::HTTP_ACCEPTED);
    }

    private function info(string $message, array $context = [])
    {
        //$this->get('monolog.logger.bitbucket')->info($message, $context);
        $this->get('logger')->info($message, $context);
    }
}
