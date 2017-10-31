<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

class CaptureRequest extends AbstractRequest
{
    protected function getRpcMethod(): string
    {
        return 'transaction.capture';
    }
    
    public function getData()
    {
        $this->validate('transactionReference');
        
        return [
            'transaction_id' => $this->getTransactionReference(),
        ];
    }
}
