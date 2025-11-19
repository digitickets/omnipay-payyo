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
        return $this->getParameter('paymentMethods');
    }

    protected function getRpcMethod(): string
    {
        return 'paymentPage.initialize';
    }

    /**
     * @param int $value
     */
    public function setExpiresInSeconds($value)
    {
        $this->setParameter('expiresInSeconds', $value);
    }

    /**
     * @return int
     */
    public function getExpiresInSeconds()
    {
        return $this->getParameter('expiresInSeconds') ?: 0;
    }

    public function getData()
    {
        $this->validate('merchantId', 'description', 'transactionId', 'returnUrl', 'cancelUrl');

        $card = $this->getCard();
        $data = [
            'merchant_id' => (int) $this->getMerchantId(),
            'merchant_reference' => $this->getTransactionId(),
            'description' => $this->getDescription(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmountInteger(),
            'return_urls' => [
                'success' => $this->getReturnUrl(),
                'error' => $this->getCancelUrl(),
                'abort' => $this->getCancelUrl()
            ],
            'language' => $this->getLanguage(),
            'customers' => [[
                'first_name' => $card->getFirstName(),
                'last_name' => $card->getLastName(),
            ]]
        ];
        if($card->getEmail()){
            $data['customers'][0]['email'] = $card->getEmail();
        }
        if($card->getPhone()){
            $data['customers'][0]['phone'] = $card->getPhone(); //^\+\d{3,19}$
        }

        if (is_array($this->getPaymentMethods()) && count($this->getPaymentMethods()) > 0) {
            $data['payment_methods'] = $this->getPaymentMethods();
        }

        if ($this->getNotifyUrl()) {
            $data['webhooks'] = [
                [
                    'url' => $this->getNotifyUrl(),
                    'method' => 'POST',
                ],
            ];
        }

        if (!empty($styling = (array) $this->getStyling())) {
            $data['styling'] = $styling;
        }

        if ($expiry = $this->getExpiresInSeconds()) {
            $data['expiration_time'] = $expiry;
        }

        return $data;
    }

    protected function createResponse(array $responseValues): ResponseInterface
    {
        return new AuthorizeResponse($this, $responseValues);
    }
}
