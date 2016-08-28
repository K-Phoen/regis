<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Regis\Domain\Entity;
use Regis\Domain\Model\Github\PullRequest;

class LoadInspectionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $inspection = Entity\Github\PullRequestInspection::create(
            $this->getReference('repository/github-test'),
            PullRequest::fromArray([
                'repository' => [
                    'owner' => 'github',
                    'name' => 'test',
                    'clone_url' => 'some clone url'
                ],
                'number' => 42,
                'revisions' => ['base' => 'base sha', 'head' => 'head sha']
            ])
        );
        $manager->persist($inspection);

        $manager->flush();

        $this->addReference('inspection/github-test-pr-42', $inspection);
    }

    public function getOrder()
    {
        return 3;
    }
}
