<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay\Message;

class RefundRequest extends AbstractRequest
{
    protected function getRpcMethod(): string
    {
        return 'transaction.refund';
    }
    
    public function getData()
    {
        $this->validate('transactionReference');
        
        return [
            'transaction_id' => $this->getTransactionReference(),
        ];
    }
}
