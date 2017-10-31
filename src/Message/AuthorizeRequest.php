<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

use Omnipay\Common\Message\ResponseInterface;

class AuthorizeRequest extends AbstractRequest
{
    /**
     * @param string $value
     */
    public function setLanguage($value)
    {
        $this->setParameter('language', $value);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->getParameter('language') ?: 'en';
    }
    
    /**
     * @param array $value
     */
    public function setStyling($value)
    {
        $this->setParameter('styling', $value);
    }

    /**
     * @return array
     */
    public function getStyling()
    {
        return $this->getParameter('styling') ?: [];
    }
    
    /**
     * @param array $value
     */
    public function setPaymentMethods($value)
    {
        $this->setParameter('paymentMethods', $value);
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
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
            'merchant_id' => (int) $this->getMerchantId(),
            'merchant_reference' => $this->getTransactionId(),
            'description' => $this->getDescription(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmountInteger(),
            'payment_methods' => (array) $this->getPaymentMethods(),
            'return_urls' => [
                'success' => $this->getReturnUrl(),
                'error' => $this->getCancelUrl(),
                'abort' => $this->getCancelUrl()
            ],
            'language' => $this->getLanguage(),
        ];
        
        if (!empty($styling = (array) $this->getStyling())) {
            $data['styling'] = $styling;
        }

        return $data;
    }
    
    protected function createResponse(array $responseValues): ResponseInterface
    {
        return new AuthorizeResponse($this, $responseValues);
    }
}
