<?php
declare(strict_types=1);

namespace Omnipay\Payyo\Message;

use Http\Discovery\Psr17FactoryDiscovery;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Payyo\PsrHttpClientAdapter;
use Payyo\Sdk\ApiClient\Client as ApiClient;
use Payyo\Sdk\ApiClient\Credentials;
use Payyo\Sdk\ApiClient\Http\ConnectionError;
use Payyo\Sdk\ApiClient\RequestError;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /** @var null|ApiClient */
    private $apiClient;
    
    /** @var null|array */
    protected $nextActionResult;

    /**
     * The RPC method to call for this request. If the request can be skipped NULL should be returned.
     * 
     * @return null|string
     */
    abstract protected function getRpcMethod(): ?string;

    /**
     * If true, the next action will be retrieved and stored in $nextActionResult before the calls to getRpcMethod() and getData().
     * 
     * @return bool
     */
    protected function isNextActionNeeded(): bool
    {
        return false;
    }
    
    public function send(): ResponseInterface
    {
        $this->initApiClient();
        
        if ($this->isNextActionNeeded() && $transactionId = $this->getTransactionReference()) {
            $responseData = $this->makeRequest('transaction.getNextAction', ['transaction_id' => $transactionId]);
            if (isset($responseData['result'])) {
                $this->nextActionResult = $responseData['result'];
            }
        }
        
        return parent::send();
    }
    
    public function sendData($data): ResponseInterface
    {
        $rpcMethod = $this->getRpcMethod();
        
        // no call required
        if ($rpcMethod === null) {
            return $this->response = $this->createResponse([
                'result' => [
                    'transaction_id' => $this->getTransactionReference(),
                ],
            ]);
        }
        
        $responseData = $this->makeRequest($this->getRpcMethod(), $data);
        
        return $this->response = $this->createResponse($responseData);
    }
    
    private function makeRequest(string $rpcMethod, array $data): array
    {
        try {
            file_put_contents(__DIR__ . '/requests.txt', $rpcMethod . PHP_EOL, FILE_APPEND);
            
            return $this->apiClient->request($rpcMethod, $data)->getValues();
        } catch (RequestError $e) {
            return [
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ],
            ];
        } catch (ConnectionError $e) {
            throw new InvalidResponseException('Connection with payment gateway failed: ' . $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException('Error communicating with payment gateway: ' . $e->getMessage(), $e->getCode());
        }
    }

    protected function createResponse(array $responseValues): Response
    {
        return new Response($this, $responseValues);
    }

    private function initApiClient(): void
    {
        if ($this->apiClient) {
            return;
        }

        $this->apiClient = new ApiClient(
            new PsrHttpClientAdapter($this->httpClient),
            Psr17FactoryDiscovery::findRequestFactory(),
            new Credentials($this->getParameter('apiKey'), $this->getParameter('secretKey'))
        );

        if ($this->getTestMode()) {
            $this->apiClient = $this->apiClient->withOtherBaseUrl('https://api.sandbox.payyo.ch');
        }
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey(string $value): self
    {
        $this->setParameter('apiKey', $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSecretKey(string $value): self
    {
        $this->setParameter('secretKey', $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantId(string $value): self
    {
        $this->setParameter('merchantId', $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->getParameter('merchantId');
    }
}
