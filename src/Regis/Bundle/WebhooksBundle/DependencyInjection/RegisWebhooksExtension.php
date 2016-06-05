<?php

namespace Regis\Bundle\WebhooksBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RegisWebhooksExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('clients.yml');
        $loader->load('inspections.yml');
        $loader->load('listeners.yml');
        $loader->load('vcs.yml');
        $loader->load('workers.yml');

        $container->setParameter('regis.config.repositories', $config['repositories']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'regis_webhooks';
    }
}