<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Symfony\Bundle\BitbucketBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RegisBitbucketExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('bitbucket.yml');
        $loader->load('repositories.yml');
        $loader->load('security.yml');
    }
}
