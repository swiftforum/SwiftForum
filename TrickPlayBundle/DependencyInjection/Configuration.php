<?php

namespace Talis\TrickPlayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('talis_trick_play');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
            ->arrayNode('options')
            ->isRequired()
            ->children()
            ->arrayNode('twig')
            ->isRequired()
            ->children()
            ->arrayNode('globals')
            ->isRequired()
            ->children()
            ->scalarNode('truepath')->defaultValue('bundles/talistrickplay')->end()
            ->booleanNode('useCDN')->defaultValue(true)->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('assetic')
            ->isRequired()
            ->children()
            ->scalarNode('read_from')->defaultValue('%kernel.root_dir%/../src/Talis/TrickPlayBundle/Resources/public/')->end()
            ->scalarNode('write_to')->defaultValue('%kernel.root_dir%/../web/bundles/talistrickplay/')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
