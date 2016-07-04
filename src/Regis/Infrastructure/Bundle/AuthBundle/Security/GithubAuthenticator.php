<?php

namespace Regis\Infrastructure\Bundle\AuthBundle\Security;

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

use Regis\Application\Command;
use Regis\Domain\Repository;

class GithubAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $usersRepo;
    private $commandBus;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, Repository\Users $usersRepo, CommandBus $commandBus, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->usersRepo = $usersRepo;
        $this->commandBus = $commandBus;
        $this->router = $router;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() !== '/login/github/check') {
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
            $githubUser->getId(),
            $githubUser->getEmail(),
            $credentials->getToken()
        );

        return $this->commandBus->handle($command);
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