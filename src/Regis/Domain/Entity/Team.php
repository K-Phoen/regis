<?php

declare(strict_types=1);

namespace Regis\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Domain\Uuid;

class Team
{
    private $id;
    private $name;
    private $owner;
    /** @var ArrayCollection */
    private $repositories;
    /** @var ArrayCollection */
    private $members;

    public function __construct(User $owner, string $name)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->id = Uuid::create();
        $this->repositories = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function isOwner(User $user): bool
    {
        return $this->owner->getId() === $user->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getRepositories(): \Traversable
    {
        return $this->repositories;
    }

    public function getMembers(): \Traversable
    {
        return $this->members;
    }

    public function addMember(User $newMember)
    {
        $this->members->add($newMember);
    }
}
