<?php
declare(strict_types=1);

namespace Omnipay\Payyo\Message;

use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
    public function isSuccessful(): bool
    {
        return isset($this->data['result']);
    }

    public function getTransactionReference()
    {
        return $this->data['result']['transaction_id'] ?? null;
    }

    public function getMessage()
    {
        return $this->data['error']['message'] ?? null;
    }

    public function getCode()
    {
        return $this->data['error']['code'] ?? null;
    }
}
