<?php

namespace HarmBandstra\SwaggerUiBundle\Tests;

use HarmBandstra\SwaggerUiBundle\HBSwaggerUiBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();

        if (in_array($this->getEnvironment(), array('test'))) {
            $bundles[] = new FrameworkBundle();
            $bundles[] = new HBSwaggerUiBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../var/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return __DIR__ . '/../var/logs';
    }
}
