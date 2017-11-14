<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Regis\GithubContext\Application\Command;
use Regis\Kernel;

class GithubAuthenticator extends SocialAuthenticator
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

        if ($requestedUrl !== $this->router->generate('github_connect_check')) {
            return null;
        }

        return $this->fetchAccessToken($this->getGithubClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GithubResourceOwner $githubUser */
        $githubUser = $this->getGithubClient()->fetchUserFromToken($credentials);

        $command = new Command\User\CreateOrUpdateUser(
            $githubUser->getNickname(),
            (int) $githubUser->getId(),
            $credentials->getToken()
        );

        /** @var Kernel\User $userProfile */
        $userProfile = $this->commandBus->handle($command);

        return $userProvider->loadUserByUsername($userProfile->accountId());
    }

    private function getGithubClient(): GithubClient
    {
        return $this->clientRegistry->getClient('github');
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
