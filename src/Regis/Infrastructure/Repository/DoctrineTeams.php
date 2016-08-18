<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

class DoctrineTeams implements Repository\Teams
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\Team $team)
    {
        $this->em->persist($team);
        $this->em->flush();
    }

    /**
     * TODO handle simple memberships
     */
    public function findForUser(Entity\User $user): \Traversable
    {
        return new \ArrayIterator($this->em->getRepository(Entity\Team::class)->findBy([
            'owner' => $user,
        ]));
    }

    public function find(string $id): Entity\Team
    {
        $team = $this->em->getRepository(Entity\Team::class)->find($id);

        if ($team === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }
        
        return $team;
    }
}
