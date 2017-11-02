<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Infrastructure\Symfony\Bundle\AnalysisBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RegisAnalysisExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('command_handlers.yml');
        $loader->load('inspections.yml');
        $loader->load('repositories.yml');
        $loader->load('vcs.yml');
        $loader->load('workers.yml');

        foreach ($config['inspections'] as $inspection => $inspectionConfig) {
            $container->setParameter('regis.config.inspections.'.$inspection, $inspectionConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'regis_analysis';
    }
}
