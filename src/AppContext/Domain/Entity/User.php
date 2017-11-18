<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Regis\Kernel;

class User implements Kernel\User, UserInterface
{
    private $id;
    private $roles;
    private $repositories;
    private $ownedTeams;
    private $teams;
    private $githubProfile;
    private $bitbucketProfile;

    public function accountId(): string
    {
        return $this->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return GithubProfile|null
     */
    public function getGithubProfile()
    {
        return $this->githubProfile;
    }

    /**
     * @return BitbucketProfile|null
     */
    public function getBitbucketProfile()
    {
        return $this->bitbucketProfile;
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
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
