<p align="center">
    <a href="https://konnect.network/" target="_blank">
        <img src="./konnect.png" height="50" alt="Konnect">
    </a>
</p>

# Konnect PHP SDK (unofficial)

The payment solution for Tunisia.
Simple, fast, personalized, immediate and for all !

## Installation

```shell
composer require machour/konnect
```

This SDK follows the PSR-18 "HTTP Client" standard, to allow for more interoperability.

It's most likely your framework will handle the dependencies injection for you, if not
you can use any implementation of the spec.

The below example uses this two librairies:

```shell
composer require guzzlehttp/guzzle nyholm/psr7
```

## SDK Initialization

```php
<?php

include './vendor/autoload.php';

use GuzzleHttp\Client;
use Machour\Konnect\Gateway;
use Machour\Konnect\ApiException;
use Nyholm\Psr7\Factory\Psr17Factory;

// Use this or bring your own implementation
$psr17Factory = new Psr17Factory();
$client = new Client();
$konnect = new Gateway($psr17Factory, $psr17Factory, $client);

// Mandatory
// Retrieve this from your Konnect dashboard
$apiKey = "6137ad140c181c5eb44a7f88:Rp2dpHPb0mBpj3_51s86zzp3PXs5w1";
$konnect->setApiKey($apiKey);

// By default, the SDK is in sandbox mode.
// To switch to production, use the following
$konnect->setProductionMode();
```

## API

### `initPayment(array $params)`

Creates a new payment request.

See [Konnect's documentation](https://api.konnect.network/api/v2/konnect-gateway#tag/Payments/paths/~1payments~1init-payment/post) for the full description of `$params` and the returned array.

```php
/**
 * @throws ApiException|\Psr\Http\Client\ClientExceptionInterface
 */
public function initPayment(array $params): array
```

<details>
<summary>See usage sample and output</summary>

```php
$response = $konnect->initPayment([
    "receiverWalletId" => "5f7a209aeb3f76490ac4a3d1",
    "description" => "payment description",
    "amount" => 100000, // millimes
    "type" => "immediate",
    "lifespan" => 10, // minutes
    "token" => "TND",
    "firstName" => "Mon prenom",
    "lastName" => "Mon nom",
    "phoneNumber" => "12345678",
    "email" => "mon.email@mail.com",
    "orderId" => "1234657",
    "link" => "https://api.dev.konnect.network/WSlQUtBF8",
    "silentWebhook" => true,
    "checkoutForm" => true,
    "webhook" => "https://merchant.tech/api/notification_payment",
    "successUrl" => "https://dev.konnect.network/gateway/payment-success",
    "failUrl" => "https://dev.konnect.network/gateway/payment-failure",
    "acceptedPaymentMethods" => [
        "bank_card",
        "wallet",
        "e-DINAR"
    ]
]);

var_dump($response);
/**
array(2) {
["payUrl"]=>
string(80) "https://preprod.konnect.network/gateway/pay?payment_ref=6392d70408ac861bcea30337"
["paymentRef"]=>
string(24) "6392d70408ac861bcea30337"
}
*/
```
</details>


### `getPaymentDetails(string $paymentId)`

Gets payment details for the specified id.

See [Konnect's documentation](https://api.konnect.network/api/v2/konnect-gateway#tag/Payments/paths/~1payments~1:paymentId/get) for the full description of the returned array.

```php
/**
 * @throws ApiException|\Psr\Http\Client\ClientExceptionInterface
 */
public function getPaymentDetails(string $paymentId): array
```

## Exceptions

This SDK throws a `\Machour\Konnect\ApiException` if there's anything wrong with your call.
The exception's `errors` property will contains the reported errors.

If however, something is wrong with the Konnect server, a `PSR-18` exception will be thrown instead.

```php
try {
    $response = $konnect->initPayment([/* ... */]);
    
    } catch (ApiException $e) {
        // HTTP status code
        echo $e->getCode();
        // HTTP status message
        echo $e->getMessage();
        // Konnect API usage errors
        var_dump($e->errors);
    
    } catch (\Psr\Http\Client\ClientExceptionInterface $e) {
        // Transport error, something is wrong with the Konnect API, and they're
        // probably already working on that
    }
}
```

## See also

* [Konnect web site](https://konnect.network/)
* [Konnect docs](https://api.konnect.network/api/v2/konnect-gateway)
