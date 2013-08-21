<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Sets up the configuration requirements
 *
 * @package Talis\SwiftForumBundle\DataFixtures\ORM
 * @author Felix Kastner <felix@chapterfain.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('talis_swift_forum');

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
                                        ->scalarNode('truepath')->defaultValue('bundles/talisswiftforum')->end()
                                        ->booleanNode('useCDN')->defaultValue(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->arrayNode('assetic')
                        ->isRequired()
                        ->children()
                            ->scalarNode('read_from')->defaultValue('%kernel.root_dir%/../src/Talis/SwiftForumBundle/Resources/public/')->end()
                            ->scalarNode('write_to')->defaultValue('%kernel.root_dir%/../web/bundles/talisswiftforum/')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
