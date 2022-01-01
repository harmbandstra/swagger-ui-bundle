<?php declare(strict_types=1);

namespace HarmBandstra\SwaggerUiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hb_swagger_ui');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('hb_swagger_ui');
        }

        $rootNode
            ->children()
            ->scalarNode('directory')->defaultValue('')->end()
            ->scalarNode('assetUrlPath')->defaultValue('/bundles/hbswaggerui/')->end()
            ->scalarNode('configFile')->defaultNull()->end()
            ->arrayNode('files')->isRequired()->prototype('scalar')->end()
            ->end();

        return $treeBuilder;
    }
}
