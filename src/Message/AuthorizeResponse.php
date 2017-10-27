<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class AuthorizeResponse extends RpcResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return false;
    }
    
    public function getRedirectUrl()
    {
        return $this->data['result']['checkout_url'] ?? null;
    }
    
    public function getRedirectMethod()
    {
        return 'GET';
    }
    
    public function getRedirectData()
    {
        return [];
    }
    
    public function isRedirect()
    {
        return $this->getRedirectUrl() !== null;
    }
}
