# Konnect PHP SDK (unofficial)

[![Konnect](./konnect.svg)](https://konnect.network)

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

See [Konnect's documentation](https://docs.konnect.network/docs/en/api-integration/endpoints/initiate-payment) for the full description of request and response data.

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
    "amount" => 10000, // millimes
    "type" => "immediate",
    "description" => "payment description",
     "acceptedPaymentMethods" => [
        "wallet",
        "bank_card",
        "e-DINAR"
    ]
    ],
    "lifespan" => 10, // minutes
    "checkoutForm" => true,
    "addPaymentFeesToAmount" => true,
    "firstName" => "John",
    "lastName" => "Doe",
    "phoneNumber" => "22777777",
    "email" => "john.doe@gmail.com",
    "orderId" => "1234657",
    "webhook" => "https://merchant.tech/api/notification_payment",
    "theme" => "dark"
]);

var_dump($response);
/**
array(2) {
["payUrl"]=>
string(83) "https://gateway.konnect.network/pay?payment_ref=6392d70408ac861bcea30337&theme=dark"
["paymentRef"]=>
string(24) "6392d70408ac861bcea30337"
}
*/
```
</details>


### `getPaymentDetails(string $paymentId)`

Gets payment details for the specified id.

See [Konnect's documentation](https://docs.konnect.network/docs/en/api-integration/endpoints/get-payment-details) for the full description of the response data.

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

* [Konnect website](https://konnect.network/)
* [Konnect docs](https://docs.konnect.network/docs/en/api-integration/intro)