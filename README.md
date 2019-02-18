Client for MIXPLAT API 3.0
=======================

With the advent of new payment methods (bank cards, electronic money, etc.), it became necessary to develop a new, universal API.

- Work with various payment methods.
- Can send both synchronous and asynchronous requests using the same interface.
- The form of payment can be both on the MIXPLAT side and on the TSP side.
- Recurring payments.

```php
$client = new \mixplat\mixplatclient\Client();

$params = [
        'payment_id' => '202cb962ac59075b964b07152d234b70',
        'signature' => 'd8d31b948ff91c896feced291ee067d7'
    ];

$payment = $client->getPayment($params);

echo $payment;

```

## Help and docs

- [Documentation](https://docs.google.com/document/d/1d8UbDm9jiSa2FmjKw5AiZS-Oi7sW3I1hFIIvzbe1hQU/edit#)


## Installing MIXPLAT Client

The recommended way to install is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of MIXPLAT Client:

```bash
php composer.phar require mixplat/mixplat-client
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update Guzzle using composer:

 ```bash
composer.phar update
 ```


## Version Guidance

| Version | Status     | Packagist                | Namespace    | Repo                | Docs                 | PSR-7 | PHP Version |
|---------|------------|--------------------------|--------------|---------------------|----------------------|-------|-------------|
| 3.0     | Latest     | `mixplat/mixplat-client` | `GuzzleHttp` | [v3][mixplat-3]     | [v3][mixplat-3-docs] | Yes   | >= 5.5      |

[mixplat-3]: https://github.com/MXPLTdev/php-client

[mixplat-3-docs]: https://docs.google.com/document/d/1d8UbDm9jiSa2FmjKw5AiZS-Oi7sW3I1hFIIvzbe1hQU/edit#
