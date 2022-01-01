<?php declare(strict_types=1);

namespace HarmBandstra\SwaggerUiBundle\Tests\Composer;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use HarmBandstra\SwaggerUiBundle\Composer\ScriptHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandlerTest extends TestCase
{
    private Prophet $prophet;

    protected function setUp(): void
    {
        $this->prophet = new Prophet;
    }

    public function testIfSwaggerAssetsAreCopiedCorrectly()
    {
        $vendorDirectory = __DIR__ . '/../../vendor';

        $config = $this->prophet->prophesize(Config::class);
        $config->get('vendor-dir')->willReturn($vendorDirectory);

        $composer = $this->prophet->prophesize(Composer::class);
        $composer->getConfig()->willReturn($config->reveal());

        $io = $this->prophet->prophesize(IOInterface::class);

        $event = $this->prophet->prophesize(Event::class);
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

    public function tearDown(): void
    {
        $this->prophet->checkPredictions();

        $filesystem = new Filesystem();
        $filesystem->remove(sprintf('%s/../../vendor/harmbandstra', __DIR__));
    }
}
