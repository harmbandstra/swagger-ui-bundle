<?php

namespace HarmBandstra\SwaggerUiBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

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

        $filesystem->mirror($source, $target, null, ['override' => true]);

        $event->getIO()->write('Linked SwaggerUI assets.');
    }
}
