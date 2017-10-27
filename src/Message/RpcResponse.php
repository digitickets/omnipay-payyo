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
        return isset($this->data['result']['transaction_id']) ? $this->data['result']['transaction_id'] : null;
    }
    
    public function getMessage()
    {
        return isset($this->data['error']['message']) ? $this->data['error']['message'] : null;
    }
    
    public function getCode()
    {
        return isset($this->data['error']['code']) ? $this->data['error']['code'] : null;
    }
}
