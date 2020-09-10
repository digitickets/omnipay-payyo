<?php

declare(strict_types=1);

namespace Omnipay\Payyo\Message;

class CaptureTransactionRequest extends AbstractRequest
{
    protected function getRpcMethod(): ?string
    {
        if (isset($this->nextActionResult['next_action']) && 'capture' === $this->nextActionResult['next_action']) {
            return 'transaction.capture';
        }

        return null;
    }

    protected function isNextActionNeeded(): bool
    {
        return true;
    }

    public function getData()
    {
        $this->validate('transactionReference');

        return [
            'transaction_id' => $this->getTransactionReference(),
        ];
    }
}
