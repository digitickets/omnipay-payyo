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
            'apiKey' => '',
            'secretKey' => '',
            'merchantId' => 0,
            'testMode' => false,
        ];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    /**
     * @param string $value
     * @return $this
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
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
