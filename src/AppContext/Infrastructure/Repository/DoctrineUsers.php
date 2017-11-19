<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Repository;

use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineUsers implements Repository\Users
{
    use RepositoryHelper;

    /** @var RulerZ */
    private $rulerz;

    public function __construct(RegistryInterface $emRegistry, RulerZ $rulerz)
    {
        $this->emRegistry = $emRegistry;
        $this->rulerz = $rulerz;
    }

    public function matching(Specification $spec): \Traversable
    {
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $this->entityManager()->getRepository(Entity\User::class);
        $qb = $repo->createQueryBuilder('u');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function findById(string $id): Entity\User
    {
        $user = $this->entityManager()->getRepository(Entity\User::class)->find($id);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }
}
