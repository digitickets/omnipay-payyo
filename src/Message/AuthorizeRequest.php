<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay\Message;

use Omnipay\Common\Message\ResponseInterface;

class AuthorizeRequest extends AbstractRequest
{
    public function setLanguage(string $value)
    {
        $this->setParameter('language', $value);
    }
    
    public function getLanguage(): string
    {
        return $this->getParameter('language') ?: 'en';
    }
    
    public function setStyling(array $styling)
    {
        $this->setParameter('styling', $styling);
    }
    
    public function getStyling(): array
    {
        return $this->getParameter('styling') ?: [];
    }
    
    public function setPaymentMethods(array $value)
    {
        $this->setParameter('paymentMethods', $value);
    }
    
    public function getPaymentMethods(): array
    {
        return $this->getParameter('paymentMethods') ?: ['credit_card'];
    }
    
    protected function getRpcMethod(): string
    {
        return 'paymentPage.initialize';
    }
    
    public function getData()
    {
        $this->validate('merchantId', 'description', 'transactionId', 'returnUrl', 'cancelUrl');
        
        $data = [
            'merchant_id' => $this->getMerchantId(),
            'merchant_reference' => $this->getTransactionId(),
            'description' => $this->getDescription(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmountInteger(),
            'payment_methods' => $this->getPaymentMethods(),
            'return_urls' => [
                'success' => $this->getReturnUrl(), 
                'error' => $this->getCancelUrl(),
                'abort' => $this->getCancelUrl()
            ],
            'language' => $this->getLanguage(),
        ];
        
        if (!empty($styling = $this->getStyling())) {
            $data['styling'] = $styling;
        }

        return $data;
    }
    
    protected function createResponse(array $responseValues): ResponseInterface
    {
        return new AuthorizeResponse($this, $responseValues);
    }
}
