<?php

namespace Regis\Infrastructure\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Regis\Domain\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = User::createAdmin('admin', 'admin');

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
