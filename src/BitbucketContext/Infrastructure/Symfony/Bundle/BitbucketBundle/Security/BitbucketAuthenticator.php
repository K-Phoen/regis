<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\BitbucketClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Regis\BitbucketContext\Application\Command;
use Regis\Kernel;

class BitbucketAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $commandBus;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, CommandBus $commandBus, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->commandBus = $commandBus;
        $this->router = $router;
    }

    public function getCredentials(Request $request)
    {
        $requestedUrl = $request->getBaseUrl().$request->getPathInfo();

        if ($requestedUrl !== $this->router->generate('bitbucket_connect_check')) {
            return null;
        }

        return $this->fetchAccessToken($this->getBitbucketClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var AccessToken $credentials */
        //var_dump($credentials);exit;
        $bitbucketUser = $this->getBitbucketClient()->fetchUserFromToken($credentials);

        $command = new Command\User\CreateOrUpdateUser(
            $bitbucketUser->getUsername(),
            $bitbucketUser->getId(),
            $credentials->getToken(),
            $credentials->getRefreshToken(),
            (new \DateTimeImmutable())->setTimestamp($credentials->getExpires())
        );

        /** @var Kernel\User $userProfile */
        $userProfile = $this->commandBus->handle($command);

        return $userProvider->loadUserByUsername($userProfile->accountId());
    }

    private function getBitbucketClient(): BitbucketClient
    {
        return $this->clientRegistry->getClient('bitbucket');
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('login'));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // TODO fixme
        return new Response('', Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('repositories_list'));
    }
}
