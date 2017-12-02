<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\AnalysisContext\Infrastructure\Git;

use Gitonomy\Git as Gitonomy;
use Psr\Log\LoggerInterface as Logger;
use Regis\AnalysisContext\Application\Vcs\Git;
use Regis\AnalysisContext\Domain\Model;
use Symfony\Component\Filesystem\Filesystem;

class Vcs implements Git
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Logger */
    private $logger;

    /** @var string */
    private $repositoriesDirectory;

    private $gitonomyOptions;

    public function __construct(Filesystem $filesystem, Logger $logger, string $gitBinary, string $repositoriesDirectory)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->repositoriesDirectory = $repositoriesDirectory;

        $this->gitonomyOptions = [
            'command' => $gitBinary,
        ];
    }

    public function getRepository(Model\Git\Repository $repository): \Regis\AnalysisContext\Application\Vcs\Repository
    {
        $repositoryPath = $this->getRepositoryPath($repository);

        if (!is_dir($repositoryPath)) {
            $gitRepo = Gitonomy\Admin::cloneTo($repositoryPath, $repository->getCloneUrl(), false, $this->gitonomyOptions);
        } else {
            $gitRepo = new Gitonomy\Repository($repositoryPath, $this->gitonomyOptions);

            // Ensure that the current remote is the right one. This is needed to fetch code from forks.
            $gitRepo->run('remote', ['set-url', 'origin', $repository->getCloneUrl()]);
        }

        $gitRepo->setLogger($this->logger);

        return new Repository($gitRepo, $this->filesystem);
    }

    private function getRepositoryPath(Model\Git\Repository $repository): string
    {
        return sprintf('%s/%s', $this->repositoriesDirectory, $repository->getIdentifier());
    }
}
