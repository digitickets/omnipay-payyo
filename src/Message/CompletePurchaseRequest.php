<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

class CompletePurchaseRequest extends AbstractRequest
{
    protected function getRpcMethod(): string
    {
        return 'transaction.capture';
    }
    
    public function getData()
    {
        return [
            'transaction_id' => $this->httpRequest->query->get('transaction_id'),
        ];
    }
}
