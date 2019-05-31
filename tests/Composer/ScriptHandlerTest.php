<?php

namespace HarmBandstra\SwaggerUiBundle\Tests\Controller;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use HarmBandstra\SwaggerUiBundle\Composer\ScriptHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandlerTest extends TestCase
{
    public function testIfSwaggerAssetsAreCopiedCorrectly()
    {
        $vendorDirectory = __DIR__ . '/../../vendor';

        $config = $this->prophesize(Config::class);
        $config->get('vendor-dir')->willReturn($vendorDirectory);

        $composer = $this->prophesize(Composer::class);
        $composer->getConfig()->willReturn($config->reveal());

        $io = $this->prophesize(IOInterface::class);

        $event = $this->prophesize(Event::class);
        $io->write(Argument::any())->shouldBeCalled();
        $event->getIO()->willReturn($io->reveal());
        $event->getComposer()->willReturn($composer->reveal());

        ScriptHandler::linkAssets($event->reveal());

        $assets = [
            'index.html',
            'swagger-ui.css',
            'swagger-ui.js',
        ];
        foreach ($assets as $asset) {
            $this->assertTrue(file_exists(
                sprintf('%s/%s/%s', $vendorDirectory, ScriptHandler::BUNDLE_PUBLIC_DIR, $asset)
            ));
        }

    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(sprintf('%s/../../vendor/harmbandstra', __DIR__));
    }
}
