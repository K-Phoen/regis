<?php

namespace Regis\Infrastructure\Bundle\WebhooksBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Regis\Application\Command;

class AddDeployKeyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:deploy-key:add')
            ->setDescription('Add a read-only deploy key to a repository.')
            ->addOption(
                'owner', 'o',
                InputOption::VALUE_REQUIRED,
                'Owner of the repository.'
            )
            ->addOption(
                'repository', 'r',
                InputOption::VALUE_REQUIRED,
                'The repository on which the key will be added.'
            )
            ->addOption(
                'public-key', null,
                InputOption::VALUE_REQUIRED,
                'Public key to add.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('owner')) {
            $input->setOption('owner', $io->ask('Who is the repository owner? (user or organization)'));
        }

        if (!$input->getOption('repository')) {
            $input->setOption('repository', $io->ask('On which repository should the webhook be added?'));
        }

        if (!$input->getOption('public-key')) {
            $input->setOption('public-key', $io->ask('Path of the public key to use'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $owner = $input->getOption('owner');
        $repo = $input->getOption('repository');
        $key = $input->getOption('public-key');

        if (!file_exists($key) || !is_readable($key)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist or is not readable', $key));
        }

        $command = new Command\Github\DeployKey\Add(
            $owner, $repo, file_get_contents($key)
        );
        $this->getContainer()->get('tactician.commandbus')->handle($command);
    }
}
