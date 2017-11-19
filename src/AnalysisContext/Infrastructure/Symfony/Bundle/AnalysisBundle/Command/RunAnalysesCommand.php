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

namespace Regis\AnalysisContext\Infrastructure\Symfony\Bundle\AnalysisBundle\Command;

use Regis\AnalysisContext\Domain\Entity\Report;
use Regis\AnalysisContext\Domain\Entity\Violation;
use Regis\AnalysisContext\Domain\Model\Git;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Regis\AnalysisContext\Application\Command;

class RunAnalysesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:analyses:run')
            ->setDescription('Runs the analyses on the given repository and revisions and displays the result.')
            ->addOption(
                'repository-identifier', null,
                InputOption::VALUE_REQUIRED,
                'Identifier of the repository..'
            )
            ->addOption(
                'clone-url', null,
                InputOption::VALUE_REQUIRED,
                'The URL to use to clone the repository.'
            )
            ->addOption(
                'base', null,
                InputOption::VALUE_REQUIRED,
                'Base commit'
            )
            ->addOption(
                'head', null,
                InputOption::VALUE_REQUIRED,
                'Head commit'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('repository-identifier')) {
            $input->setOption('repository-identifier', $io->ask('Repository identifier?'));
        }

        if (!$input->getOption('clone-url')) {
            $input->setOption('clone-url', $io->ask('Clone URL?'));
        }

        if (!$input->getOption('head')) {
            $input->setOption('head', $io->ask('Head commit?'));
        }

        if (!$input->getOption('base')) {
            $input->setOption('base', $io->ask('Base commit?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $revision = new Git\Revisions($input->getOption('base'), $input->getOption('head'));
        $repository = new Git\Repository(
            $input->getOption('repository-identifier'),
            $input->getOption('clone-url')
        );

        $command = new Command\RunAnalyses($repository, $revision);
        /** @var Report $report */
        $report = $this->getContainer()->get('tactician.commandbus')->handle($command);

        $table = new Table($output);
        $table->setStyle('borderless');
        $table->setHeaders(['Inspection', 'File', 'Line', 'Message']);

        /** @var Violation $violation */
        foreach ($report->violations() as $violation) {
            $table->addRow([
                $violation->analysis()->type(),
                $violation->file(),
                $violation->line(),
                $violation->description(),
            ]);
        }

        $table->render();
    }
}
