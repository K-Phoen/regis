<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;

class SendReportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:github:send-report')
            ->setDescription('Send a report to Github, transforming violations to comments.')
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
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('inspection')) {
            $input->setOption('inspection', $io->ask('Inspection identifier?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inspection = $this->findInspection($input->getOption('inspection'));

        $output->writeln(sprintf('Sending violations ...'));

        $command = new Command\Inspection\SendViolationsAsComments($inspection);
        $this->getContainer()->get('tactician.commandbus')->handle($command);

        $output->writeln('<info>Done.</info>');
    }

    private function findInspection($identifier): Entity\PullRequestInspection
    {
        return $this->getContainer()->get('regis.github.repository.pull_request_inspections')->find($identifier);
    }
}
