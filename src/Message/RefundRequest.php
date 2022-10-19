<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

class RefundRequest extends AbstractRequest
{
    protected function getRpcMethod(): string
    {
        return 'transaction.refund';
    }

    public function getData()
    {
        $this->validate('transactionReference');

        $data = [
            'transaction_id' => $this->getTransactionReference(),
        ];

        if ($this->getAmountInteger() > 0) {
            $data['amount'] = $this->getAmountInteger();
        }

        return $data;
    }
}
