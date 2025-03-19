# Mixplat API PHP Client Library

Клиент для работы [API Mixplat](https://mixplat.ru/)

Документация и описание: [docs.mixplat.ru](https://docs.mixplat.ru)

## Требования
PHP 5.3 (и выше)

## Установка
### В консоли с помощью Composer

1. Установите менеджер пакетов Composer.
2. В консоли выполните команду
```bash
composer require mixplat/mixplat-php-client
```

### В файле composer.json своего проекта
1. Добавьте строку `"mixplat/mixplat-php-client": "*"` в список зависимостей вашего проекта в файле composer.json
```
...
    "require": {
        "mixplat/mixplat-php-client": "*"
...
```
2. Обновите зависимости проекта. В консоли перейдите в каталог, где лежит composer.json, и выполните команду:
```bash
composer update
```
3. В коде вашего проекта подключите автозагрузку файлов нашего клиента:
```php
require __DIR__ . '/vendor/autoload.php';
```

### Вручную

1. Скачайте [архив Mixplat API PHP Client](https://github.com/MXPLTdev/mixplat-php-client/archive/master.zip), распакуйте его и скопируйте каталог src в нужное место в вашем проекте.
2. В коде вашего проекта подключите автозагрузку файлов нашего клиента:
```php
require __DIR__ . '/src/autoload.php'; 
```

## Начало работы

Создайте и заполните конфигурацию подключения
```php
$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;
$mixplatConfiguration->companyId = $companyId;
$mixplatConfiguration->companyApiKey = $companyApiKey;
```
Создайте экземпляр объекта клиента, укажите ему конфигурацию
```php
$httpClient = new \MixplatClient\HttpClient\SimpleHttpClient();
$mixplatClient = new \MixplatClient\MixplatClient();
$mixplatClient->setConfig($mixplatConfiguration);
$mixplatClient->setHttpClient($httpClient);
```
Создайте экземпляр метода API и задайте ему необходимые атрибуты
```php
$apiRequest = new \MixplatClient\Method\CreatePaymentForm();

$apiRequest->amount             = 300;
$apiRequest->description        = 'Тестовая оплата';
$apiRequest->userEmail          = 'user@mail.ru';
$apiRequest->userName           = 'Константин Константинопольский';
$apiRequest->userPhone          = '79991234567';
$apiRequest->test               = 1;
```
Вызовите метод
```php
$response = $mixplatClient->request($apiRequest);
```
## Перенаправление на платёжную форму

При обращении к методу `CreatePaymentForm` и получении ссылки на платёжную форму вида `$response['redirect_url']` для переадресации плательщика на эту форму используйте редирект с использованием JavaScript:

```php
echo "<script>window.location.replace('" . $response['redirect_url'] . "');</script>";
exit;
```
Использование редиректа с помощью `header`:

```php
header("Location: ".$response['redirect_url']);
exit;
```

может вызывать некорректное поведение в некоторых встроенных браузерах (in-app browsers), используемых в мобильных приложениях.

См. подробнее в [примере](https://github.com/MXPLTdev/mixplat-php-client/blob/master/examples/example_create_payment_form.php).