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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Regis\GithubContext\Application\Command;

class AddDeployKeyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:github:add-deploy-key')
            ->setDescription('Add a read-only deploy key to a Github repository.')
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

        $command = new Command\Repository\AddDeployKey(
            $owner, $repo, file_get_contents($key)
        );
        $this->getContainer()->get('tactician.commandbus')->handle($command);
    }
}
