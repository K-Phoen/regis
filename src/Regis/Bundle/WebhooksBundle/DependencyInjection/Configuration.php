<?php

namespace Regis\Bundle\WebhooksBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('regis_webhooks');

        $this->addInspectionsSection($rootNode);

        return $treeBuilder;
    }

    private function addInspectionsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('inspections')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcs')
                            ->children()
                                ->arrayNode('options')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('phpmd')
                            ->children()
                                ->arrayNode('rulesets')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}