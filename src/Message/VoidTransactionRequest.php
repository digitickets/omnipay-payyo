<?php
declare(strict_types=1);

namespace Omnipay\Payyo\Message;

class VoidTransactionRequest extends AbstractRequest
{
    protected function getRpcMethod(): ?string
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
