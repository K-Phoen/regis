<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Symfony\Bundle\GithubBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RegisGithubExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('command_handlers.yml');
        $loader->load('github.yml');
        $loader->load('random.yml');
        $loader->load('reporters.yml');
        $loader->load('listeners.yml');
        $loader->load('repositories.yml');
        $loader->load('security.yml');
        $loader->load('workers.yml');
    }
}
