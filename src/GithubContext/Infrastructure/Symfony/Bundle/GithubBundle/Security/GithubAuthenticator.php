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
