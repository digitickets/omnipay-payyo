<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay;

use Guzzle\Http\ClientInterface as Guzzle3Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface as Request;
use TrekkPay\Sdk\ApiClient\Http\Client;

class Guzzle3Adapter implements Client
{
    /** @var Guzzle3Client */
    private $guzzle3Client;

    public function __construct(Guzzle3Client $guzzle3Client)
    {
        $this->guzzle3Client = $guzzle3Client;
    }

    public function request(Request $request)
    {
        $guzzle3Request = $this->guzzle3Client->createRequest(
            $request->getMethod(),
            (string) $request->getUri(),
            $request->getHeaders(),
            (string) $request->getBody()
        );
        
        $guzzle3Response = $this->guzzle3Client->send($guzzle3Request);
        
        return new Response(
            $guzzle3Response->getStatusCode(),
            $guzzle3Response->getHeaders()->toArray(),
            $guzzle3Response->getBody()->__toString(),
            $guzzle3Response->getProtocolVersion(),
            $guzzle3Response->getReasonPhrase()
        );
    }
}
