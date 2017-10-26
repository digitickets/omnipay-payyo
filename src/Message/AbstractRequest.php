<?php
declare(strict_types=1);

namespace Omnipay\TrekkPay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\TrekkPay\Guzzle3Adapter;
use TrekkPay\Sdk\ApiClient\Client as ApiClient;
use TrekkPay\Sdk\ApiClient\Credentials;
use TrekkPay\Sdk\ApiClient\Http\ConnectionError;
use TrekkPay\Sdk\ApiClient\RequestError;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /** @var ApiClient|null */
    private $apiClient;
    
    public function setApiKey(string $value)
    {
        return $this->setParameter('apiKey', $value);
    }
    
    public function getApiKey(): string
    {
        return $this->getParameter('apiKey');
    }
    
    public function setSecretKey(string $value)
    {
        return $this->setParameter('secretKey', $value);
    }
    
    public function getSecretKey(): string
    {
        return $this->getParameter('secretKey');
    }
    
    public function setMerchantId(int $value)
    {
        return $this->setParameter('merchantId', $value);
    }
    
    public function getMerchantId(): int
    {
        return $this->getParameter('merchantId');
    }
    
    abstract protected function getRpcMethod(): string;
    
    public function sendData($data)
    {
        $this->initApiClient();
        
        try {
            $response = $this->apiClient->request($this->getRpcMethod(), $data);
            
            return $this->response = $this->createResponse($response->getValues());
        } catch (RequestError $e) {
            return $this->response = $this->createResponse([
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ],
            ]);
        } catch (ConnectionError $e) {
            throw new InvalidResponseException('Connection with payment gateway failed: ' . $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException('Error communicating with payment gateway: ' . $e->getMessage(), $e->getCode());
        }
    }
    
    protected function createResponse(array $responseValues): ResponseInterface
    {
        return new RpcResponse($this, $responseValues);
    }
    
    private function initApiClient()
    {
        if ($this->apiClient) {
            return;
        }
        
        $this->apiClient = new ApiClient(
            new Credentials($this->getParameter('apiKey'), $this->getParameter('secretKey')),
            null,
            new Guzzle3Adapter($this->httpClient)
        );

        if ($this->getTestMode()) {
            $this->apiClient = $this->apiClient->withOtherBaseUrl('https://api.sandbox.trekkpay.io');
        }
    }
}
