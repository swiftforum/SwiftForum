<?php

namespace Talis\TrickPlayBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TalisTrickPlayExtension extends Extension implements PrependExtensionInterface
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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
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

        if (isset($this->ext_config['talis_trick_play']['options']['assetic']) && isset($bundles['AsseticBundle'])) {
            $container->prependExtensionConfig('assetic', $this->ext_config['talis_trick_play']['options']['assetic']);
        }

        if (isset($this->ext_config['talis_trick_play']['options']['twig']) && isset($bundles['TwigBundle'])) {
            $container->prependExtensionConfig('twig', $this->ext_config['talis_trick_play']['options']['twig']);
        }

    }
}
