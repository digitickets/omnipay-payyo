# Omnipay: Payyo

**Payyo Gateway for the Omnipay PHP payment processing library.**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+.

The Payyo Omnipay library requires PHP 7.0+.

## Installation

Omnipay can be installed using [Composer](https://getcomposer.org/). [Installation instructions](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

Run the following command to install omnipay and the Payyo gateway:

    composer require digitickets/omnipay-payyo

## Basic Usage

The following parameters are required:

- `apiKey` Your Payyo API/public key
- `secretKey` Your Payyo secret key
- `merchantId` Your Payyo merchant ID

```php
$gateway = Omnipay::create('Payyo');
$gateway->setApiKey('api_...');
$gateway->setSecretKey('sec_...');
$gateway->setMerchantId('1234');
$gateway->setTestMode(true);

// Send purchase request
$response = $gateway->purchase([
    'transactionId' => '123456',
    'description' => '1x Book',
    'amount' => '10.00',
    'currency' => 'USD',
    'paymentMethods' => ['credit_card'],    
    'returnUrl' => 'https://example.org/success',
    'cancelUrl' => 'https://example.org/abort',
])->send();

// This is a redirect gateway, so redirect right away
$response->redirect();
```
## Requests

### Purchase
* **purchase()** calls `paymentPage.initialize`, then you should redirect
* **completePurchase()** calls `transaction.getNextAction` and (if necessary) `transaction.capture`

### Authorize + Capture
* **authorize()** calls `paymentPage.initialize`, then you should redirect
* **completeAuthorize()** calls `transaction.getDetails`
* **capture()** calls `transaction.getNextAction` and (if necessary) `transaction.capture`

### Void/Refund
* **void()** calls `transaction.void`
* **refund()** calls `transaction.refund`

## Testing

You can run `docker-compose up` and then go to `http://localhost:8086/` to make a test payment against the Sandbox.