<?php

namespace Regis\Application\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Regis\Application\Entity;

class DoctrineInspections implements Inspections
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\Inspection $inspections)
    {
        $this->em->persist($inspections);
        $this->em->flush();
    }

    public function find(string $id): Entity\Inspection
    {
        $inspection = $this->em->getRepository(Entity\Inspection::class)->find($id);

        if ($inspection === null) {
            throw Exception\NotFound::forIdentifier($id);
        }
        
        return $inspection;
    }
}
