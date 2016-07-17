<?php

namespace Regis\Infrastructure\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Regis\Domain\Entity;

class LoadRepositoryData extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $repository = new Entity\Github\Repository($this->getReference('user/user'), 'github/test', 'fake shared secret');
        $manager->persist($repository);

        $manager->flush();

        $this->addReference('repository/github-test', $repository);
    }
}
