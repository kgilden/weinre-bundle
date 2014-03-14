KGWeinreBundle
==============

`KGWeinreBundle` integrates Weinre with your Symfony2 application.

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
## Requirements

* PHP >= 5.3.8

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) file.

## Running the Tests

You can run unit tests, you'll need to install dev dependencies:

```
php composer.phar install --dev
```

Once installed, just launch the following command:

```
phpunit
```

....

## License

`KGWeinreBundle` is released under the MIT License.
See the bundled [LICENSE](LICENSE) file for details.
