<?php

namespace Tests\Infrastructure\Regis\Vcs;

use Gitonomy\Git as Gitonomy;

use Regis\Domain\Model\Git as Model;
use Regis\Infrastructure\Vcs\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $gitonomyRepository = $this->getMockBuilder(Gitonomy\Repository::class)->disableOriginalConstructor()->getMock();
        $repository = new Repository($gitonomyRepository);

        $gitonomyRepository->expects($this->once())
            ->method('run')
            ->with('fetch');

        $repository->update();
    }

    public function testGetDiff()
    {
        $gitonomyRepository = new Gitonomy\Repository(APP_ROOT_DIR);
        $repository = new Repository($gitonomyRepository);
        $revisions = new Model\Revisions('HEAD', 'HEAD^');

        $this->assertInstanceOf(Model\Diff::class, $repository->getDiff($revisions));
    }
}
