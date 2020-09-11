<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay;

use Omnipay\Common\AbstractGateway;
use TrekkPay\Omnipay\Message\AuthorizeRequest;
use TrekkPay\Omnipay\Message\CaptureRequest;
use TrekkPay\Omnipay\Message\CompleteAuthorizeRequest;
use TrekkPay\Omnipay\Message\CompletePurchaseRequest;
use TrekkPay\Omnipay\Message\RefundRequest;
use TrekkPay\Omnipay\Message\VoidRequest;

class HostedPaymentPageGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Payyo Hosted Payment Page';
    }

    public function getDefaultParameters()
    {
        return [
            'apiKey' => '',
            'secretKey' => '',
            'merchantId' => 0,
            'testMode' => false,
            'domain' => 'trekkpay.io',
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

    /**
     * @param string $value
     * @return $this
     */
    public function setDomain($value)
    {
        $this->setParameter('domain', $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->getParameter('domain');
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
