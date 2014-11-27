<?php
/*
 * This file is part of the Egils\Cache package.
 *
 * (c) Egidijus Lukauskas <egils.ps@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Egils\Bundle\CacheBundle\DependencyInjection;

use Egils\Component\Cache\InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class EgilsCacheExtension extends Extension
{
    /** @var Definition */
    private $cacheManagerDefinition;

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $rootConfig = $this->processConfiguration($configuration, $config);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->cacheManagerDefinition = $container->getDefinition('egils_cache.cache_manager');

        $this->loadDoctrineAdapters($rootConfig);
        $this->loadAdapters($rootConfig);
        $this->setDefaultAdapter($rootConfig);
    }

    private function loadDoctrineAdapters(array $rootConfig)
    {
        foreach ($rootConfig['doctrine_providers'] as $name) {
            $this->cacheManagerDefinition->addMethodCall(
                'addDoctrineCacheProvider',
                [$name, new Reference('doctrine_cache.providers.' . $name)]
            );
        }
    }

    private function loadAdapters(array $rootConfig)
    {
        foreach ($rootConfig['adapters'] as $name => $config) {
            if (null !== $config['service']) {
                $this->cacheManagerDefinition->addMethodCall(
                    'addAdapter',
                    [$name, new Reference($config['service'])]
                );
            } elseif (null !== $config['class'] && true === class_exists($config['class'])) {
                $adapter = new $config['class']();

                if (false === $adapter instanceof CacheItemPoolInterface) {
                    throw new InvalidArgumentException(
                        'Class provided by "egils_cache.adapters.' . $name .
                        '" must implement \Psr\Cache\CacheItemPoolInterface'
                    );
                }

                $this->cacheManagerDefinition->addMethodCall(
                    'addAdapter',
                    [$name, $adapter]
                );
            }
        }
    }

    private function setDefaultAdapter(array $rootConfig)
    {
        if (null !== $rootConfig['default']) {
            $this->cacheManagerDefinition->addMethodCall('setDefaultAdapterName', [$rootConfig['default']]);
        }
    }
}
