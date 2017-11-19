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

namespace Regis\AppContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Kernel\Uuid;

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

    public function removeMember(User $member)
    {
        $this->members->removeElement($member);
    }

    public function addRepository(Repository $repository)
    {
        $this->repositories->add($repository);
    }

    public function removeRepository(Repository $repository)
    {
        $this->repositories->removeElement($repository);
    }
}
