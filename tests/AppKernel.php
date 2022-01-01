<?php declare(strict_types=1);

namespace HarmBandstra\SwaggerUiBundle\Tests;

use HarmBandstra\SwaggerUiBundle\HBSwaggerUiBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        $bundles = array();

        if (in_array($this->getEnvironment(), array('test'))) {
            $bundles[] = new FrameworkBundle();
            $bundles[] = new HBSwaggerUiBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }

    public function getCacheDir(): string
    {
        return __DIR__ . '/../var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return __DIR__ . '/../var/logs';
    }
}
