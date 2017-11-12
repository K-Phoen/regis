<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

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
    private $bitbucketId;
    private $bitbucketAccessToken;
    private $roles = [];
    private $repositories;
    private $ownedTeams;
    private $teams;

    public static function createUser(string $username, int $bitbucketId, string $bitbucketAccessToken): self
    {
        $user = new static($username);
        $user->bitbucketId = $bitbucketId;
        $user->bitbucketAccessToken = $bitbucketAccessToken;
        $user->roles = ['ROLE_USER'];

        return $user;
    }

    private function __construct(string $username)
    {
        $this->id = Uuid::create();
        $this->username = $username;

        $this->repositories = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getBitbucketId()
    {
        return $this->bitbucketId;
    }

    /**
     * @return string|null
     */
    public function getBitbucketAccessToken()
    {
        return $this->bitbucketAccessToken;
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
        return null;
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
