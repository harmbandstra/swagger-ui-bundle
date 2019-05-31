[![Build Status](https://travis-ci.org/harmbandstra/swagger-ui-bundle.svg?branch=master)](https://travis-ci.org/harmbandstra/swagger-ui-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/harmbandstra/swagger-ui-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/harmbandstra/swagger-ui-bundle/?branch=master)

# Swagger UI Bundle

Expose swagger-ui inside your symfony project through a route (eg. /docs), just like [nelmio api docs](https://github.com/nelmio/NelmioApiDocBundle), without the need for node.

Just add a reference to your swagger Yaml or JSON specification, and enjoy swagger-ui in all it's glory.

After installation and configuration, just start your local webserver, and navigate to [/docs](http://127.0.0.1:8000/docs) or [/docs/my_swagger_spec.yml](http://127.0.0.1:8000/docs/my_swagger_spec.yml).

## Compatibility

* If you need symfony 2.3 - 2.6 support, use version 1.x.
* If you need symfony 2.7 - 3.x support, or php 5.x use version 2.x.
* For symfony 3.3 and later with PHP > 7.0 use version 3.x.
* For symfony 4.0 and later with PHP => 7.1.3 use version 4.x.

**NOTE** Since version 3.1, support for symfony 4 on the 3.x branch has been dropped. Use the 4.x branch instead.

## Installation

Install with composer in dev environment:

`$ composer require harmbandstra/swagger-ui-bundle --dev`

Make sure swagger-ui assets are copied to `web/bundles` by adding the [`HarmBandstra\SwaggerUiBundle\Composer\ScriptHandler::linkAssets`](src/Composer/ScriptHandler#L13) composer hook **before** the [`Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installAssets`](https://github.com/sensiolabs/SensioDistributionBundle/blob/master/Composer/ScriptHandler.php#L158) hook in your `composer.json`.

```json
{
  "scripts": {
    "symfony-scripts": [
        "HarmBandstra\\SwaggerUiBundle\\Composer\\ScriptHandler::linkAssets",
        "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets"
    ],
    "post-install-cmd": ["@symfony-scripts"],
    "post-update-cmd": ["@symfony-scripts"]
}
```

If the `scripts` section in composer.json looks like this (symfony 4):
```json
    "scripts": {
        "auto-scripts": {
            "cache:clear": "symfony-cmd",
            "assets:install %PUBLIC_DIR%": "symfony-cmd"
        },
        "post-install-cmd": [
            "@auto-scripts"
        ],
        "post-update-cmd": [
            "@auto-scripts"
        ]
    },
```

Add the composer hook like this:
```json
    "scripts": {
        "auto-scripts": {
            "cache:clear": "symfony-cmd",
            "assets:install %PUBLIC_DIR%": "symfony-cmd"
        },
        "post-install-cmd": [
            "HarmBandstra\\SwaggerUiBundle\\Composer\\ScriptHandler::linkAssets",
            "@auto-scripts"
        ],
        "post-update-cmd": [
            "HarmBandstra\\SwaggerUiBundle\\Composer\\ScriptHandler::linkAssets",
            "@auto-scripts"
        ]
    },
```

Enable bundle in `app/AppKernel.php`(Symfony 3):

```php
<?php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        // ...

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            // ...
            $bundles[] = new HarmBandstra\SwaggerUiBundle\HBSwaggerUiBundle();
        }

        // ...
    }
}
```

Enable bundle in `config/bundles.php`(Symfony 4):
```php
<?php

return [
    // ...
    HarmBandstra\SwaggerUiBundle\HBSwaggerUiBundle::class => ['dev' => true]
];
```
Add the route where swagger-ui will be available in `routing_dev.yml`:

```yml
_swagger-ui:
    resource: '@HBSwaggerUiBundle/Resources/config/routing.yml'
    prefix: /docs
```

## Configuration (Symfony 3)

In your `config.yml`, link to the swagger spec.

Specify the `directory` where your swagger files reside. You can access multiple files through the endpoint like `/docs/my_swagger_spec.json`.
Under `files` you specify which files should be exposed.

The first file in the array is the default one and it will be the file the `/docs` endpoint will redirect to. For this file you have the option to specify an absolute path to the .json spec file ("/_swagger/swagger.json") or a URL ("https://example.com/swagger.json").

```yaml
hb_swagger_ui:
  directory: "%kernel.root_dir%/../docs/"
  files:
    - "/_swagger/swagger.json"
    - "my_swagger_spec.yml"
    - "my_other_swagger_spec.json"
```

## Configuration (Symfony 4)

Create a file `hb_swagger_ui.yaml` `in config/packages`. Follow the rest of the steps for configuration in Symfony 3.
