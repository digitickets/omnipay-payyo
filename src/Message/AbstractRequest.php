<?php
declare(strict_types=1);

namespace TrekkPay\Omnipay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use TrekkPay\Omnipay\Guzzle3Adapter;
use TrekkPay\Sdk\ApiClient\Client as ApiClient;
use TrekkPay\Sdk\ApiClient\Credentials;
use TrekkPay\Sdk\ApiClient\Http\ConnectionError;
use TrekkPay\Sdk\ApiClient\RequestError;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /** @var ApiClient|null */
    private $apiClient;
    
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

    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        $this->setParameter('apiKey', $value);
        
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    /**
     * @param string $value
     * @return $this
     */
    public function setSecretKey($value)
    {
        $this->setParameter('secretKey', $value);
        
        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantId($value)
    {
        $this->setParameter('merchantId', $value);
        
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }
}
