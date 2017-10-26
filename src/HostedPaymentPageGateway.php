<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\TrekkPay\Message\AuthorizeRequest;
use Omnipay\TrekkPay\Message\CaptureRequest;
use Omnipay\TrekkPay\Message\CompleteAuthorizeRequest;
use Omnipay\TrekkPay\Message\CompletePurchaseRequest;
use Omnipay\TrekkPay\Message\RefundRequest;
use Omnipay\TrekkPay\Message\VoidRequest;

class HostedPaymentPageGateway extends AbstractGateway
{
    public function getName()
    {
        return 'TrekkPay Hosted Payment Page';
    }
    
    public function getDefaultParameters()
    {
        return [
            'apiKey' => null,
            'secretKey' => null,
            'merchantId' => null,
            'testMode' => false,
        ];
    }
    
    public function setApiKey(string $value)
    {
        return $this->setParameter('apiKey', $value);
    }
    
    public function setSecretKey(string $value)
    {
        return $this->setParameter('secretKey', $value);
    }
    
    public function setMerchantId(int $value)
    {
        return $this->setParameter('merchantId', $value);
    }
    
    public function purchase(array $parameters = [])
    {
        return $this->authorize($parameters);
    }
    
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }
    
    public function authorize(array $parameters = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $parameters);
    }
    
    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest(CompleteAuthorizeRequest::class, $parameters);
    }
    
    public function capture(array $parameters = [])
    {
        return $this->createRequest(CaptureRequest::class, $parameters);
    }
    
    public function void(array $parameters = [])
    {
        return $this->createRequest(VoidRequest::class, $parameters);
    }
    
    public function refund(array $parameters = [])
    {
        return $this->createRequest(RefundRequest::class, $parameters);
    }
}
