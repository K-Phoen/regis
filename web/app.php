<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../app/autoload.php';

$kernel = new AppKernel('prod', false);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();

// Heroku-specific. See https://devcenter.heroku.com/articles/getting-started-with-symfony#trusting-the-load-balancer
Request::setTrustedProxies(array($request->server->get('REMOTE_ADDR')));
Request::setTrustedHeaderName(Request::HEADER_FORWARDED, null);
Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, null);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
