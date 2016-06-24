<?php

namespace Regis\Application\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Regis\Application\Model;

class DoctrineRepositories implements Repositories
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Model\Repository $repository)
    {
        $this->em->persist($repository);
        $this->em->flush();
    }

    public function findAll(): \Traversable
    {
        return new \ArrayIterator($this->em->getRepository(Model\Repository::class)->findAll());
    }

    public function find(string $identifier): Model\Repository
    {
        $repository = $this->em->getRepository(Model\Repository::class)->find($identifier);

        if ($repository === null) {
            throw Exception\NotFound::forIdentifier($identifier);
        }
        
        return $repository;
    }
}
