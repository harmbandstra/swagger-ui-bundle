<?php

namespace HarmBandstra\SwaggerUiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HBSwaggerUiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('harm_bandstra_swagger_ui.directory', $config['directory']);
        $container->setParameter('harm_bandstra_swagger_ui.default_file', $config['default_file']);
    }
}
