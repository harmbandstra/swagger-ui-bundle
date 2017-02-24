<?php

namespace HarmBandstra\SwaggerUiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hb_swagger_ui');

        $rootNode
            ->children()
            ->scalarNode('directory')->end()
            ->scalarNode('default_file')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
