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

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Command;

use Regis\GithubContext\Domain\Model;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;

class SchedulePullRequestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('regis:github:schedule-pull-request')
            ->setDescription('Schedules the given GitHub pull request for inspection.')
            ->addOption(
                'repository', 'r',
                InputOption::VALUE_REQUIRED,
                'Name of the repository.'
            )
            ->addOption(
                'pull-request', null,
                InputOption::VALUE_REQUIRED,
                'Pull request number'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('repository')) {
            $input->setOption('repository', $io->ask('Repository identifier?'));
        }

        if (!$input->getOption('pull-request')) {
            $input->setOption('pull-request', $io->ask('Pull request number?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $repositoryEntity = $this->findRepository($input->getOption('repository'));

        $output->writeln(sprintf('Fetching details for GitHub PR #%d', (int) $input->getOption('pull-request')));
        $pullRequest = $this->findPrDetails($repositoryEntity, (int) $input->getOption('pull-request'));

        $output->writeln(sprintf('Scheduling inspection PR #%d (base: %s, HEAD: %s)', $pullRequest->getNumber(), $pullRequest->getBase(), $pullRequest->getHead()));

        $command = new Command\Inspection\SchedulePullRequest($pullRequest);
        $this->getContainer()->get('tactician.commandbus')->handle($command);

        $output->writeln('<info>Done.</info>');
    }

    private function findRepository($identifier): Entity\Repository
    {
        return $this->getContainer()->get('regis.github.repository.repositories')->find($identifier);
    }

    private function findPrDetails(Entity\Repository $repository, int $prNumber): Model\PullRequest
    {
        $githubClient = $this->getContainer()->get('regis.github.client_factory')->createForRepository($repository);
        $prDetails = $githubClient->getPullRequestDetails($repository->toIdentifier(), $prNumber);

        return new Model\PullRequest(
            $repository->toIdentifier(),
            $prNumber,
            $prDetails['head']['sha'],
            $prDetails['base']['sha']
        );
    }
}
