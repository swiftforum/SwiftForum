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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class TalisSwiftForumExtension extends Extension implements PrependExtensionInterface
{

    private $ext_config;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!empty($this->ext_config) && is_array($this->ext_config)) {
            $configs = array_merge($configs, $this->ext_config);
        }

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }


    /**
     * Automatically fixes the paths for the assets used in this bundle.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $env = $container->getParameter('kernel.environment');
        $bundles = $container->getParameter('kernel.bundles');

        if ($env === 'prod') {
            $this->ext_config = Yaml::parse(__DIR__.'/../Resources/config/config.yml');
        } else {
            $this->ext_config = Yaml::parse(__DIR__.'/../Resources/config/config_dev.yml');
        }

        if (isset($this->ext_config['talis_swift_forum']['options']['assetic']) && isset($bundles['AsseticBundle'])) {
            $container->prependExtensionConfig('assetic', $this->ext_config['talis_swift_forum']['options']['assetic']);
        }

        if (isset($this->ext_config['talis_swift_forum']['options']['twig']) && isset($bundles['TwigBundle'])) {
            $container->prependExtensionConfig('twig', $this->ext_config['talis_swift_forum']['options']['twig']);
        }

    }
}
