<?php

namespace Regis\Application\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Regis\Application\Entity;

class DoctrineRepositories implements Repositories
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\Repository $repository)
    {
        $this->em->persist($repository);
        $this->em->flush();
    }

    public function findAll(): \Traversable
    {
        return new \ArrayIterator($this->em->getRepository(Entity\Repository::class)->findAll());
    }

    public function find(string $id): Entity\Repository
    {
        $repository = $this->em->getRepository(Entity\Repository::class)->find($id);

        if ($repository === null) {
            throw Exception\NotFound::forIdentifier($id);
        }
        
        return $repository;
    }
}
