<?php

/*
 * This file is part of the KGWeinreBundle package.
 *
 * (c) Kristen Gilden <kristen.gilden@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\WeinreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kristen Gilden <kristen.gilden@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('kg_weinre');

        $root
            ->children()
                ->scalarNode('scheme')->defaultValue('http')->end()
                ->scalarNode('host')->defaultNull()->end()
                ->scalarNode('port')->defaultValue('8080')->end()
                ->scalarNode('path')->defaultValue('/target/target-script-min.js')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
