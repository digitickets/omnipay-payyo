<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

class VoidRequest extends AbstractRequest
{
    protected function getRpcMethod(): string
    {
        return 'transaction.void';
    }
    
    public function getData()
    {
        $this->validate('transactionReference');
        
        return [
            'transaction_id' => $this->getTransactionReference(),
        ];
    }
}
