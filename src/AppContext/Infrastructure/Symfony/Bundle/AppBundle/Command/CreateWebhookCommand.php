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

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Regis\AppContext\Domain\Entity;

class CreateWebhookCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:setup-webhook')
            ->setDescription('Setup a webhook for the given repository.')
            ->addOption(
                'repository', 'r',
                InputOption::VALUE_REQUIRED,
                'ID of the repository  on which the hook will be created.'
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
        $repository = $this->findRepository($input->getOption('repository'));

        $this->getContainer()->get('regis.app.remote.actions')->createWebhook(
            $repository,
            $this->generateWebhookUrl($repository)
        );
    }

    private function generateWebhookUrl(Entity\Repository $repository): string
    {
        return $this->getContainer()->get('router')->generate(
            $repository->getType().'_webhook',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function findRepository(string $id): Entity\Repository
    {
        return $this->getContainer()->get('regis.app.repository.repositories')->find($id);
    }
}
