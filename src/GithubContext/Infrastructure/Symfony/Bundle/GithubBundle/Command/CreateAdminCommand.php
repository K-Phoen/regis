<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Regis\GithubContext\Application\Command;

class CreateAdminCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:users:create-admin')
            ->setDescription('Create an admin')
            ->addOption(
                'username', 'u',
                InputOption::VALUE_REQUIRED,
                'Its username.'
            )
            ->addOption(
                'email', 'm',
                InputOption::VALUE_REQUIRED,
                'Its email.'
            )
            ->addOption(
                'password', 'p',
                InputOption::VALUE_REQUIRED,
                'Its password.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('username')) {
            $input->setOption('username', $io->ask('Username'));
        }

        if (!$input->getOption('password')) {
            $input->setOption('password', $io->askHidden('Password'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new Command\User\CreateAdmin(
            $input->getOption('username'),
            $input->getOption('password')
        );

        $this->getContainer()->get('tactician.commandbus')->handle($command);
    }
}
