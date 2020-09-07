<?php
declare(strict_types=1);

namespace Omnipay\Payyo\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class InitializePaymentPageResponse extends Response implements RedirectResponseInterface
{
    public function isSuccessful(): bool
    {
        return false;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->data['result']['checkout_url'] ?? null;
    }

    public function isRedirect(): bool
    {
        return $this->getRedirectUrl() !== null;
    }
}
