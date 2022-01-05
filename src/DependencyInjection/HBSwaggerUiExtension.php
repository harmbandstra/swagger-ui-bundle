<?php

namespace HarmBandstra\SwaggerUiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HBSwaggerUiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('hb_swagger_ui.directory', $config['directory']);
        $container->setParameter('hb_swagger_ui.files', $config['files']);
        $container->setParameter('hb_swagger_ui.configFile', $config['configFile']);
    }
}
