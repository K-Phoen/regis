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
        $this->addRepositoriesSection($rootNode);

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
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addRepositoriesSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('repositories')
                    ->isRequired()
                    ->useAttributeAsKey('identifier')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('identifier')->end()
                            ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}