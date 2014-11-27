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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('egils_cache');

        $rootNode
            ->children()
                ->arrayNode('doctrine_providers')
                    ->prototype('scalar')->end()
                ->end()

                ->arrayNode('adapters')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')->defaultNull()->end()
                            ->scalarNode('class')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('default')
                    ->cannotBeEmpty()
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
