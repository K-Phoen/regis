<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

trait RepositoryHelper
{
    /** @var RegistryInterface */
    private $emRegistry;

    public function __construct(RegistryInterface $emRegistry)
    {
        $this->emRegistry = $emRegistry;
    }

    private function entityManager(): EntityManagerInterface
    {
        return $this->emRegistry->getManager();
    }
}
