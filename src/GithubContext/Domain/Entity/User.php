<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Kernel\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @todo all the symfony and doctrine-related stuff should be in the infrastructure layer
 */
class User implements UserInterface
{
    private $id;
    private $username;
    private $roles = [];
    private $password;
    private $repositories;
    private $ownedTeams;
    private $teams;

    /** @var GithubDetails */
    private $details;

    public static function createAdmin(string $username, string $password): User
    {
        $user = new static($username);
        $user->changePassword($password);
        $user->roles = ['ROLE_ADMIN'];

        return $user;
    }

    public static function createUser(string $username, int $githubId, string $githubAccessToken): User
    {
        $user = new static($username);
        $details = new GithubDetails($user, $githubId, $githubAccessToken);
        $user->details = $details;
        $user->roles = ['ROLE_USER'];

        return $user;
    }

    private function __construct(string $username)
    {
        $this->id = Uuid::create();
        $this->username = $username;

        $this->repositories = new ArrayCollection();
    }

    public function changePassword(string $password)
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('The new password can not be empty');
        }

        $this->password = $password;
    }

    public function changeGithubAccessToken(string $accessToken)
    {
        $this->details->changeGithubAccessToken($accessToken);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDetails(): GithubDetails
    {
        return $this->details;
    }

    /**
     * @return int|null
     */
    public function getGithubId()
    {
        return $this->details->getRemoteId();
    }

    /**
     * @return string|null
     */
    public function getGithubAccessToken()
    {
        return $this->details->getAccessToken();
    }

    public function getRepositories(): \Traversable
    {
        return $this->repositories;
    }

    public function getOwnedTeams(): \Traversable
    {
        return $this->ownedTeams;
    }

    public function getTeams(): \Traversable
    {
        return $this->teams;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }
}
