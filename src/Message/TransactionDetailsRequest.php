<?php

declare(strict_types=1);

namespace Omnipay\Payyo\Message;

class TransactionDetailsRequest extends AbstractRequest
{
    protected function getRpcMethod(): ?string
    {
        return 'transaction.getDetails';
    }

    public function getData()
    {
        return [
            'transaction_id' => $this->getTransactionReference(),
        ];
    }
}
