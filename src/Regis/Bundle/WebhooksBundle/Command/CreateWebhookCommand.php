<?php

namespace Regis\Bundle\WebhooksBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateWebhookCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:webhooks:create')
            ->setDescription('Create a webhook using GitHub API')
            ->addOption(
                'owner', 'o',
                InputOption::VALUE_REQUIRED,
                'Owner of the repository.'
            )
            ->addOption(
                'repository', 'r',
                InputOption::VALUE_REQUIRED,
                'The repository on which the hook will be created.'
            )
            ->addOption(
                'host', null,
                InputOption::VALUE_REQUIRED,
                'Public host for the webhook.'
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

        if (!$input->getOption('host')) {
            $input->setOption('host', $io->ask('What is the public host to use to join this application?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $owner = $input->getOption('owner');
        $repo = $input->getOption('repository');
        $host = rtrim($input->getOption('host'), '/');
        $absoluteUrl = $host.$this->getContainer()->get('router')->generate('webhook_github');
        $config = $this->getRepositoryConfig($owner.'/'.$repo);

        $this->getContainer()->get('regis.github.client')->createWebhook($owner, $repo, $absoluteUrl, $config['secret']);
    }

    private function getRepositoryConfig(string $repository): array
    {
        $config = $this->getContainer()->getParameter('regis.config.repositories');

        if (empty($config[$repository])) {
            throw new \InvalidArgumentException(sprintf('Repository "%s" not found in the configuration.', $repository));
        }

        return $config[$repository];
    }
}
