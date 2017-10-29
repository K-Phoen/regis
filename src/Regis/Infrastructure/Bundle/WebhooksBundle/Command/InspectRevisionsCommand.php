<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\WebhooksBundle\Command;

use Regis\Domain\Entity\Inspection\Report;
use Regis\Domain\Entity\Inspection\Violation;
use Regis\Domain\Model\Git;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Regis\Application\Command;

class InspectRevisionsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:inspect:revisions')
            ->setDescription('Inspects the given repository and revisions and displays the result.')
            ->addOption(
                'repository-owner', 'o',
                InputOption::VALUE_REQUIRED,
                'Name of the repository owner.'
            )
            ->addOption(
                'repository-name', 'r',
                InputOption::VALUE_REQUIRED,
                'Name of the repository.'
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
            $input->getOption('repository-owner'),
            $input->getOption('repository-name'),
            $input->getOption('clone-url')
        );

        $command = new Command\Git\InspectRevisions($repository, $revision);
        /** @var Report $report */
        $report = $this->getContainer()->get('tactician.commandbus')->handle($command);

        $table = new Table($output);
        $table->setStyle('borderless');
        $table->setHeaders(['Inspection', 'File', 'Line', 'Message']);

        /** @var Violation $violation */
        foreach ($report->getViolations() as $violation) {
            $table->addRow([
                $violation->getAnalysis()->getType(),
                $violation->getFile(),
                $violation->getLine(),
                $violation->getDescription(),
            ]);
        }

        $table->render();
    }
}
