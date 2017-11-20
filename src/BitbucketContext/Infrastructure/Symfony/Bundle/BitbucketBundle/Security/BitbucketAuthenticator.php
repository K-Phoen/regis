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
use Symfony\Component\Security\Core\User\UserInterface;
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

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        /** @var AccessToken $credentials */
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
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // TODO fixme
        return new Response('', Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        return new RedirectResponse($this->router->generate('repositories_list'));
    }
}
