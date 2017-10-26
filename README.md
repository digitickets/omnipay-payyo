# Omnipay: TrekkPay

**TrekkPay Gateway for the Omnipay PHP payment processing library.**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+.

The TrekkPay Omnipay library requires PHP 7.0+.

## Installation

Omnipay can be installed using [Composer](https://getcomposer.org/). [Installation instructions](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

Run the following command to install omnipay and the TrekkPay gateway:

    composer require trekkpay/omnipay

## Basic Usage

The following parameters are required:

- `apiKey` Your TrekkPay API/public key
- `secretKey` Your TrekkPay secret key
- `merchantId` Your TrekkPay merchant ID

```php
$gateway = Omnipay::create('TrekkPay_HostedPaymentPage');
$gateway->setApiKey('api_...');
$gateway->setSecretKey('sec_...');
$gateway->setMerchantId(1234);

// Send purchase request
$response = $gateway->purchase([
    'transactionId' => '123456',
    'description' => '1x Book',
    'amount' => '10.00',
    'currency' => 'USD',
    'returnUrl' => 'https://example.org/success',
    'cancelUrl' => 'https://example.org/abort',
])->send();

// This is a redirect gateway, so redirect right away
$response->redirect();
```