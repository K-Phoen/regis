<?php

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
    protected function configure()
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
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('repository')) {
            $input->setOption('repository', $io->ask('Repository identifier?'));
        }

        if (!$input->getOption('pull-request')) {
            $input->setOption('pull-request', $io->ask('Repository identifier?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
