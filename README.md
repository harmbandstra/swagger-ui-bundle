[![Build Status](https://travis-ci.org/harmbandstra/swagger-ui-bundle.svg?branch=master)](https://travis-ci.org/harmbandstra/swagger-ui-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/harmbandstra/swagger-ui-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/harmbandstra/swagger-ui-bundle/?branch=master)

# Swagger UI Bundle

Expose swagger-ui inside your symfony project through a route (eg. /docs), just like [nelmio api docs](https://github.com/nelmio/NelmioApiDocBundle), without the need for node.

Just add a reference to your swagger Yaml or JSON specification, and enjoy swagger-ui in all it's glory.

After installation and configuration, just start your local webserver, and navigate to [/docs](http://127.0.0.1:8000/docs) or [/docs/my_swagger_spec.yml](http://127.0.0.1:8000/docs/my_swagger_spec.yml).

## Installation

Install with composer in dev environment:

`$ composer require harmbandstra/swagger-ui-bundle --dev`

Enable bundle in `app/AppKernel.php`:

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

Add the route where swagger-ui will be available in `routing_dev.yml`:

```yml
_swagger-ui:
    resource: '@HBSwaggerUiBundle/Resources/config/routing.yml'
    prefix: /docs
```

## Configuration

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
