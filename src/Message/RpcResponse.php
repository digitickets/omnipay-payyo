<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay\Message;

use Omnipay\Common\Message\AbstractResponse;

class RpcResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['result']);
    }
    
    public function getTransactionReference()
    {
        return $this->data['result']['transaction_id'] ?? null;
    }
}
