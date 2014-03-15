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
$ php composer.phar require kgilden/weinre-bundle:dev-master
```

Or add it manually to `composer.json`

```json
{
    "require": {
        "kgilden/weinre-bundle": "dev-master"
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

....

## License

`KGWeinreBundle` is released under the MIT License.
See the bundled [LICENSE](LICENSE) file for details.
