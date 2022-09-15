<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class CompletePurchaseRequest extends AbstractRequest
{
    // The webhook types that we want to run a transaction.capture against. Any others will fail.
    const WEBHOOK_TYPES_TO_RUN = [
        'TransactionAbortedEvent',
        'TransactionAuthorizedEvent',
        'TransactionVoidedEvent',
        // Transaction was directly authorized (no 3D Secure)
        'TransactionAuthorizedDirectEvent',
        'TransactionVoidedEvent',
    ];

    protected function getRpcMethod(): string
    {
        return 'transaction.capture';
    }

    public function getData()
    {
        return [
            'transaction_id' => $this->getTransactionIdFromRequest(),
        ];
    }

    /**
     * Returns transaction_id from either query string (from a redirect), or post content (from a webhook)
     * @return string|null
     * @throws InvalidRequestException - can be thrown if this webhook type is not in the list from self::WEBHOOK_TYPES_TO_RUN is passed in
     */
    protected function getTransactionIdFromRequest()
    {
        $transactionID = $this->httpRequest->query->get('transaction_id');

        // If this is from a webhook, we get the full transaction details in the post body
        if (!$transactionID && $this->httpRequest->getMethod() === 'POST') {
            $json = json_decode($this->httpRequest->getContent(), true);
            if (!empty($json['event']) && !empty($json['event']['type'])) {
                $event = $json['event'];
                // Check this is a webhook type we want to handle
                if (in_array($event['type'], self::WEBHOOK_TYPES_TO_RUN)) {
                    if (!empty($event['data'])) {
                        $transactionID = $event['data']['transaction_id'] ?? '';
                    }
                    if (!$transactionID) {
                        throw new InvalidRequestException('Missing transaction_id in webhook data: '.json_encode($event));
                    }
                } else {
                    throw new InvalidRequestException(sprintf('This webhook type (%s) cannot be handled, please only pass in types: %s', $event['type'], implode(', ', self::WEBHOOK_TYPES_TO_RUN)));
                }
            }
        }

        return $transactionID;

    }

}
