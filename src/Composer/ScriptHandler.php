<?php

namespace HarmBandstra\SwaggerUiBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function linkAssets(Event $event)
    {
        $filesystem = new Filesystem();
        $vendorDir = __DIR__ . '/../../../..';

        $source = sprintf('%s/swagger-api/swagger-ui/dist', $vendorDir);
        $target = sprintf('%s/harmbandstra/swagger-ui-bundle/src/Resources/public', $vendorDir);

        $exclude = new Finder();
        $exclude->files()->in($source)->notName('*.map');

        $filesystem->mirror($source, $target, $exclude, ['override' => true]);

        $event->getIO()->write('Linked SwaggerUI assets.');
    }
}
