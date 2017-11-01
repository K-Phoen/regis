<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Repository;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Repository\Repositories;

class DefineSharedSecret
{
    private $repositoriesRepo;

    public function __construct(Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\DefineSharedSecret $command)
    {
        $command->getRepository()->newSharedSecret($command->getNewSharedSecret());

        $this->repositoriesRepo->save($command->getRepository());
    }
}
