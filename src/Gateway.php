<?php

declare(strict_types=1);

namespace Omnipay\Payyo;

use Omnipay\Common\AbstractGateway;
use Omnipay\Payyo\Message\CaptureTransactionRequest;
use Omnipay\Payyo\Message\InitializePaymentPageRequest;
use Omnipay\Payyo\Message\RefundTransactionRequest;
use Omnipay\Payyo\Message\TransactionDetailsRequest;
use Omnipay\Payyo\Message\VoidTransactionRequest;

class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'Payyo Hosted Payment Page';
    }

    public function getDefaultParameters(): array
    {
        return [
            'apiKey' => '',
            'secretKey' => '',
            'merchantId' => '',
        ];
    }

    /**
     * @return $this
     */
    public function setApiKey(string $value): self
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getApiKey(): string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return $this
     */
    public function setSecretKey(string $value): self
    {
        return $this->setParameter('secretKey', $value);
    }

    public function getSecretKey(): string
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @return $this
     */
    public function setMerchantId(string $value): self
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantId(): string
    {
        return $this->getParameter('merchantId');
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest(InitializePaymentPageRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CaptureTransactionRequest::class, $parameters);
    }

    public function authorize(array $parameters = [])
    {
        return $this->createRequest(InitializePaymentPageRequest::class, $parameters);
    }

    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest(TransactionDetailsRequest::class, $parameters);
    }

    public function capture(array $parameters = [])
    {
        return $this->createRequest(CaptureTransactionRequest::class, $parameters);
    }

    public function void(array $parameters = [])
    {
        return $this->createRequest(VoidTransactionRequest::class, $parameters);
    }

    public function refund(array $parameters = [])
    {
        return $this->createRequest(RefundTransactionRequest::class, $parameters);
    }
}
