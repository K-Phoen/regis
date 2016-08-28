<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;

use Regis\Domain\Entity;
use Regis\Domain\Repository;

class DoctrineInspections implements Repository\Inspections
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
            throw Repository\Exception\NotFound::forIdentifier($id);
        }
        
        return $inspection;
    }
}
