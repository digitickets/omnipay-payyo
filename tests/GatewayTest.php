<?php

declare(strict_types=1);

namespace Omnipay\Tests\Payyo;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Payyo\Gateway;
use Omnipay\Payyo\Message\InitializePaymentPageResponse;
use Omnipay\Payyo\Message\Response;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    protected $initializeData = [
        'transactionId' => '123456',
        'description' => 'Test booking',
        'amount' => '200.00',
        'currency' => 'USD',
        'returnUrl' => 'https://example.org/success',
        'cancelUrl' => 'https://example.org/abort',
    ];

    protected $completeData = [
        'transactionReference' => 'tra_6975671a2b81a3fb0d385486c994',
    ];

    protected $refundData = [
        'transactionReference' => 'tra_6975671a2b81a3fb0d385486c994',
    ];

    protected $voidData = [
        'transactionReference' => 'tra_6975671a2b81a3fb0d385486c994',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize([
            'apiKey' => 'api_0000000000000000000000000000',
            'secretKey' => 'sec_0000000000000000000000000000',
            'merchantId' => 1234,
        ]);
    }

    public function testAuthorizeSuccess(): void
    {
        $this->setMockHttpResponse('PaymentPageInitializeSuccess.txt');
        $response = $this->gateway->authorize($this->initializeData)->send();
        self::assertInstanceOf(InitializePaymentPageResponse::class, $response);
        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isRedirect());
        self::assertNull($response->getTransactionReference());
        self::assertSame('https://checkout.payyo.ch/pp/pp_d901071cfd48dcbc1a3fef1cc399', $response->getRedirectUrl());
    }

    public function testAuthorizeFailure(): void
    {
        $this->setMockHttpResponse('PaymentPageInitializeFailure.txt');
        $response = $this->gateway->authorize($this->initializeData)->send();
        self::assertInstanceOf(InitializePaymentPageResponse::class, $response);
        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertNull($response->getTransactionReference());
        self::assertSame('Request validation failed: amount:minimum', $response->getMessage());
    }

    public function testCompleteAuthorizeSuccess(): void
    {
        $this->setMockHttpResponse('TransactionDetailsSuccess.txt');
        $response = $this->gateway->completeAuthorize($this->completeData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }

    public function testCaptureSuccess(): void
    {
        $this->setMockHttpResponse('TransactionCaptureSuccess.txt');
        $response = $this->gateway->capture($this->completeData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }

    public function testPurchaseSuccess(): void
    {
        $this->setMockHttpResponse('PaymentPageInitializeSuccess.txt');
        $response = $this->gateway->purchase($this->initializeData)->send();
        self::assertInstanceOf(InitializePaymentPageResponse::class, $response);
        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isRedirect());
        self::assertNull($response->getTransactionReference());
        self::assertSame('https://checkout.payyo.ch/pp/pp_d901071cfd48dcbc1a3fef1cc399', $response->getRedirectUrl());
    }

    public function testPurchaseFailure(): void
    {
        $this->setMockHttpResponse('PaymentPageInitializeFailure.txt');
        $response = $this->gateway->purchase($this->initializeData)->send();
        self::assertInstanceOf(InitializePaymentPageResponse::class, $response);
        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertNull($response->getTransactionReference());
        self::assertSame('Request validation failed: amount:minimum', $response->getMessage());
    }

    public function testCompletePurchaseSuccess(): void
    {
        $this->setMockHttpResponse('TransactionDetailsSuccess.txt');
        $response = $this->gateway->completePurchase($this->completeData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }

    public function testVoidSuccess(): void
    {
        $this->setMockHttpResponse('TransactionVoidSuccess.txt');
        $response = $this->gateway->void($this->voidData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }

    public function testVoidFailure(): void
    {
        $this->setMockHttpResponse('TransactionVoidFailure.txt');
        $response = $this->gateway->void($this->voidData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertFalse($response->isSuccessful());
        self::assertSame('Transaction must be AUTHORIZED, but is VOIDED.', $response->getMessage());
    }

    public function testRefundSuccess(): void
    {
        $this->setMockHttpResponse('TransactionRefundSuccess.txt');
        $response = $this->gateway->refund($this->refundData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }

    public function testRefundFailure(): void
    {
        $this->setMockHttpResponse('TransactionRefundFailure.txt');
        $response = $this->gateway->refund($this->refundData)->send();
        self::assertInstanceOf(Response::class, $response);
        self::assertFalse($response->isSuccessful());
        self::assertSame('Transaction is already fully refunded', $response->getMessage());
    }
}
