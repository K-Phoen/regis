<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Regis\Domain\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = User::createAdmin('admin', 'admin', 'admin@admin');
        $manager->persist($userAdmin);

        $user = User::createUser('user', 'user@foo.org', 42, 'fake access token');
        $manager->persist($user);

        $manager->flush();

        $this->addReference('user/user', $user);
    }

    public function getOrder()
    {
        return 1;
    }
}
