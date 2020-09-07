<?php
declare(strict_types=1);

namespace Omnipay\Payyo;

use Psr\Http\Client\ClientInterface;
use Omnipay\Common\Http\ClientInterface as OmnipayClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PsrHttpClientAdapter implements ClientInterface
{
    private $client;

    public function __construct(OmnipayClient $client)
    {
        $this->client = $client;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $body = $request->getBody();
        $body->rewind();
        
        return $this->client->request(
            $request->getMethod(),
            (string) $request->getUri(),
            $request->getHeaders(),
            $body->getContents()
        );
    }
}
