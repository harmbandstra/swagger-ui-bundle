<?php

namespace HarmBandstra\SwaggerUiBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScriptHandler
{
    const SWAGGER_UI_DIST_DIR = 'swagger-api/swagger-ui/dist';
    const BUNDLE_PUBLIC_DIR = 'harmbandstra/swagger-ui-bundle/src/Resources/public';

    /**
     * @param Event $event
     */
    public static function linkAssets(Event $event)
    {
        $filesystem = new Filesystem();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        $source = sprintf('%s/%s', $vendorDir, self::SWAGGER_UI_DIST_DIR);
        $target = sprintf('%s/%s', $vendorDir, self::BUNDLE_PUBLIC_DIR);

        $filesIterator = new Finder();
        $filesIterator->files()->in($source)->notName('*.map');

        $filesystem->mirror($source, $target, $filesIterator, ['override' => true]);

        $event->getIO()->write('Linked SwaggerUI assets.');
    }
}
