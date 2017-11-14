<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RegisAppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('auth_providers.yml');
        $loader->load('command_handlers.yml');
        $loader->load('logs.yml');
        $loader->load('remote.yml');
        $loader->load('repositories.yml');
        $loader->load('security.yml');
    }
}
