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
use Regis\AnalysisContext\Application\Vcs\FileNotFound;
use Regis\AnalysisContext\Application\Vcs\Repository as VcsRepository;
use Regis\AnalysisContext\Domain\Model\Git as Model;
use Symfony\Component\Filesystem\Filesystem;

class Repository implements VcsRepository
{
    /** @var Gitonomy\Repository */
    private $repository;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(Gitonomy\Repository $repository, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
    }

    public function checkout(string $revision)
    {
        $this->repository->run('fetch');
        $this->repository->getWorkingCopy()->checkout($revision);
    }

    public function locateFile(string $name): string
    {
        $repositoryPath = $this->repository->getPath();
        $filePath = $repositoryPath.'/'.$name;

        if (!$this->filesystem->exists($filePath)) {
            throw FileNotFound::inRepository($repositoryPath, $name);
        }

        return $filePath;
    }

    public function getDiff(Model\Revisions $revisions): Model\Diff
    {
        $gitDiff = $this->repository->getDiff(sprintf('%s..%s', $revisions->getBase(), $revisions->getHead()));

        $diff = Model\Diff::fromRawDiff($revisions, $gitDiff->getRawDiff());

        return $this->augmentWithFileContents($diff);
    }

    private function augmentWithFileContents(Model\Diff $diff): Model\Diff
    {
        $files = array_map(function (Model\Diff\File $file) {
            if ($file->isDeletion() || $file->isRename()) {
                return $file;
            }

            $blob = $this->repository->getBlob($file->getNewIndex());

            return $file->replaceNewContent(
                new Model\Blob($blob->getHash(), $blob->getContent(), $blob->getMimetype())
            );
        }, $diff->getFiles());

        return new Model\Diff($diff->getRevisions(), $files, $diff->getRawDiff());
    }
}
