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
                ->scalarNode('directory')->defaultValue('')->end()
                ->scalarNode('configFile')->defaultNull()->end()
                ->arrayNode('files')->isRequired()->prototype('scalar')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
