# Installation
Composer

```bash
composer require coolrunner/php-sdk
```

# Usage

Full documentation available in the [Wiki](https://github.com/CoolRunner-dk/coolrunner-php-sdk/wiki)

## Hinting
Full PhpDoc support should be available for most IDEs

## Instanciation
CoolRunner SDK for API v3 needs to be instantiated before it can be used. 

It is a singleton, and requires your registered email and designated token to be usable.

Get you API token here: [Integration](https://coolrunner.dk/customer/integration/)

If the page is unaccessible please contact our support

```php
$api = CoolRunnerSDK\API::load('<your@email.here>', '<your token here>');
```