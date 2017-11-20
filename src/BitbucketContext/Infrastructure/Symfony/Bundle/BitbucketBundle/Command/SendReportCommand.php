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

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Domain\Entity;

class SendReportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('regis:bitbucket:send-report')
            ->setDescription('Send a report to Bitbucket, transforming violations to comments.')
            ->addOption(
                'inspection', 'i',
                InputOption::VALUE_REQUIRED,
                'Identifier of the inspection to report.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('inspection')) {
            $input->setOption('inspection', $io->ask('Inspection identifier?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $inspection = $this->findInspection($input->getOption('inspection'));

        $output->writeln(sprintf('Sending violations ...'));

        $command = new Command\Inspection\SendViolationsAsComments($inspection);
        $this->getContainer()->get('tactician.commandbus')->handle($command);

        $output->writeln('<info>Done.</info>');
    }

    private function findInspection($identifier): Entity\PullRequestInspection
    {
        return $this->getContainer()->get('regis.bitbucket.repository.pull_request_inspections')->find($identifier);
    }
}
