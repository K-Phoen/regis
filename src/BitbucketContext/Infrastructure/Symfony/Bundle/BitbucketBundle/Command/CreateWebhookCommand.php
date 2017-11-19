<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\Command;

use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Regis\BitbucketContext\Application\Command;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreateWebhookCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:bitbucket:create-webhook')
            ->setDescription('Create a webhook using Bitbucket API')
            ->addOption(
                'repository', 'r',
                InputOption::VALUE_REQUIRED,
                'The repository on which the hook will be created.'
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
            $input->setOption('repository', $io->ask('On which repository should the webhook be added?'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $absoluteUrl = $this->getContainer()->get('router')->generate('bitbucket_webhook', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $command = new Command\Repository\CreateWebhook(
            new RepositoryIdentifier($input->getOption('repository')),
            $absoluteUrl
        );

        $this->getContainer()->get('tactician.commandbus')->handle($command);
    }
}
