# Swagger UI Bundle

Expose swagger-ui inside your symfony project through a route (eg. /docs), just like [nelmio api docs](https://github.com/nelmio/NelmioApiDocBundle), without the need for node.

Just add a reference to your swagger Yaml or JSON specification, and enjoy swagger-ui in all it's glory.

Note: Yaml support is experimental.

After installation and configuration, just start your local webserver, and navigate to [/docs](http://127.0.0.1:8000/docs)

## Installation

Install with composer:

`$ composer require harmbandstra/swagger-ui-bundle`

Enable bundle in `app/AppKernel.php`:

```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...
            new HarmBandstra\SwaggerUiBundle\HarmBandstraSwaggerUiBundle(),
```

Add the route where swagger-ui will be available in `routing.yml`:

```yml
hb_swagger_ui:
    resource: '@HarmBandstraSwaggerUiBundle/Resources/config/routing.yml'
    prefix: /docs
```

## Configuration

In your `config.yml`, link to the swagger spec.

Specify the `directory` where your swagger files reside. You can access multiple files through the endpoint like `/docs/my_swagger_spec.json`.
If you specify a `default_swagger_file` the `/docs` endpoint will redirect to this file.

```yaml
hb_swagger_ui:
  directory: "%kernel.root_dir%/../docs/"
  default_file: "my_swagger_spec.json"
```
