<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay\Message;

class CompleteAuthorizeRequest extends AbstractRequest
{
    protected function getRpcMethod(): string
    {
        return 'transaction.getDetails';
    }
    
    public function getData()
    {
        return [
            'transaction_id' => $this->httpRequest->query->get('transaction_id'),
        ];
    }
}
