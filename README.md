KGWeinreBundle
==============

[![Build Status](https://travis-ci.org/kgilden/weinre-bundle.png)](https://travis-ci.org/kgilden/weinre-bundle)

`KGWeinreBundle` integrates [Weinre](http://people.apache.org/~pmuellr/weinre/)
with your Symfony2 application to enable mobile debugging.

**NB!** To make this work with your mobile devices you still need to install
Weinre, please consult [their docs](http://people.apache.org/~pmuellr/weinre/docs/latest/)
on how to do that.

## Installation

Add `KGWeinerBundle` to your app with Composer:

```bash
$ php composer.phar require kgilden/weinre-bundle:~1.0
```

Or add it manually to `composer.json`

```json
{
    "require": {
        "kgilden/weinre-bundle": "~1.0"
    }
}
```

... and then install our dependencies using:
```bash
$ php composer.phar install
```

Finally enable the bundle in the dev environment:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
    );

    if ('dev' === $this->getEnvironment()) {
        $bundles[] = new KG\WeinreBundle\KGWeinreBundle();
    }
}

?>
```

## Configuration

By default the bundle expects your Weinre server to run on the same machine. For
example, if the application runs at 198.51.100.0, the target script is expected
to be at "http://198.51.100.0:8080/target/target-script-min.js". You can
override this by configuring the bundle (each value is optional):

```yml
kg_weinre:
    scheme: https        # defaults to 'http'
    host:   203.0.113.0  # defaults to server address
    port:   8000         # defaults to '8080'
    path:   /foo.js      # defaults to '/target/target-script-min.js'
```

## Requirements

* PHP >= 5.3.8
* symfony/http-kernel >= 2.0.0
* symfony/event-dispatcher >= 2.0.0
* symfony/dependency-injecton >= 2.0.0
* symfony/config >= 2.0.0

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) file.

## Running the Tests

You can run unit tests by simply executing

```
phpunit
```

## Credits

Thanks to [Inoryy](https://github.com/Inoryy) for the
[project skeleton](https://github.com/Inoryy/php-project-skeleton).

....

## License

`KGWeinreBundle` is released under the MIT License.
See the bundled [LICENSE](LICENSE) file for details.
