<?php

declare(strict_types=1);

namespace Regis\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Regis\Domain\Uuid;

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

    public static function createAdmin(string $username, string $password): User
    {
        $user = new static($username, $password);
        $user->roles = ['ROLE_ADMIN'];

        return $user;
    }

    private function __construct(string $username, string $password)
    {
        $this->id = Uuid::create();
        $this->username = $username;

        $this->changePassword($password);

        $this->repositories = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRepositories(): \Traversable
    {
        return $this->repositories;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function changePassword(string $password)
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('The new password can not be empty');
        }

        $this->password = $password;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }
}
