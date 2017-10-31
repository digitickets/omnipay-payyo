<?php
declare(strict_types=1);

namespace Omnipay\Tests\TrekkPay;

use Omnipay\Tests\GatewayTestCase;
use TrekkPay\Omnipay\HostedPaymentPageGateway;
use TrekkPay\Omnipay\Message\AuthorizeResponse;
use TrekkPay\Omnipay\Message\RpcResponse;

class HostedPaymentPageGatewayTest extends GatewayTestCase
{
    /** @var HostedPaymentPageGateway */
    protected $gateway;
    protected $authorizeOptions = [];
    protected $captureOptions = [];
    protected $refundOptions = [];
    protected $voidOptions = [];
    
    public function setUp()
    {
        parent::setUp();
        
        $this->gateway = new HostedPaymentPageGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize([
            'apiKey' => 'api_0000000000000000000000000000',
            'secretKey' => 'sec_0000000000000000000000000000',
            'merchantId' => 1234,
        ]);
        $this->authorizeOptions = [
            'transactionId' => '123456',
            'description' => 'Test booking',
            'amount' => '200.00',
            'currency' => 'USD',
            'returnUrl' => 'https://example.org/success',
            'cancelUrl' => 'https://example.org/abort',
        ];
        $this->captureOptions = [
            'transactionReference' => 'tra_6975671a2b81a3fb0d385486c994',
        ];
        $this->refundOptions = [
            'transactionReference' => 'tra_6975671a2b81a3fb0d385486c994',
        ];
        $this->voidOptions = [
            'transactionReference' => 'tra_6975671a2b81a3fb0d385486c994',
        ];
    }
    
    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('HppAuthorizeSuccess.txt');
        $response = $this->gateway->authorize($this->authorizeOptions)->send();
        $this->assertInstanceOf(AuthorizeResponse::class, $response);
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('https://checkout.trekkpay.io/pp/pp_d901071cfd48dcbc1a3fef1cc399', $response->getRedirectUrl());
    }
    
    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('HppAuthorizeFailure.txt');
        $response = $this->gateway->authorize($this->authorizeOptions)->send();
        $this->assertInstanceOf(AuthorizeResponse::class, $response);
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('Request validation failed: amount:minimum', $response->getMessage());
    }
    
    public function testCompleteAuthorizeSuccess()
    {
        $this->setMockHttpResponse('HppCompleteAuthorizeSuccess.txt');
        $response = $this->gateway->completeAuthorize($this->authorizeOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }
    
    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('HppCaptureSuccess.txt');
        $response = $this->gateway->capture($this->captureOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }
    
    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('HppCaptureFailure.txt');
        $response = $this->gateway->capture($this->captureOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
    }
    
    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('HppAuthorizeSuccess.txt');
        $response = $this->gateway->purchase($this->authorizeOptions)->send();
        $this->assertInstanceOf(AuthorizeResponse::class, $response);
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('https://checkout.trekkpay.io/pp/pp_d901071cfd48dcbc1a3fef1cc399', $response->getRedirectUrl());
    }
    
    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('HppAuthorizeFailure.txt');
        $response = $this->gateway->purchase($this->authorizeOptions)->send();
        $this->assertInstanceOf(AuthorizeResponse::class, $response);
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('Request validation failed: amount:minimum', $response->getMessage());
    }
    
    public function testCompletePurchaseSuccess()
    {
        $this->setMockHttpResponse('HppCaptureSuccess.txt');
        $response = $this->gateway->completePurchase()->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }
    
    public function testVoidSuccess()
    {
        $this->setMockHttpResponse('HppVoidSuccess.txt');
        $response = $this->gateway->void($this->voidOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }
    
    public function testVoidFailure()
    {
        $this->setMockHttpResponse('HppVoidFailure.txt');
        $response = $this->gateway->void($this->voidOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Transaction must be AUTHORIZED, but is VOIDED.', $response->getMessage());
    }
    
    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('HppRefundSuccess.txt');
        $response = $this->gateway->refund($this->refundOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('tra_6975671a2b81a3fb0d385486c994', $response->getTransactionReference());
    }
    
    public function testRefundFailure()
    {
        $this->setMockHttpResponse('HppRefundFailure.txt');
        $response = $this->gateway->refund($this->refundOptions)->send();
        $this->assertInstanceOf(RpcResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Transaction is already fully refunded', $response->getMessage());
    }
}
