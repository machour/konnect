<?php

declare(strict_types=1);

namespace Machour\Konnect;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Gateway
{
    /** @var string  */
    private $apiKey;
    /** @var \Psr\Http\Message\RequestFactoryInterface */
    private $requestFactory;
    /** @var \Psr\Http\Message\StreamFactoryInterface */
    private $streamFactory;
    /** @var \Psr\Http\Client\ClientInterface */
    private $httpClient;
    /** @var bool */
    private $productionMode;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface  $streamFactory,
        ClientInterface         $httpClient
    )
    {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->httpClient = $httpClient;
    }

    /**
     * @throws ApiException|\Psr\Http\Client\ClientExceptionInterface
     */
    public function initPayment(array $params): array
    {
        return $this->callApi("POST", 'payments/init-payment', $params);
    }

    /**
     * @throws ApiException|\Psr\Http\Client\ClientExceptionInterface
     */
    public function getPaymentDetails(string $paymentId): array
    {
        return $this->callApi("GET", "payments/$paymentId");
    }

    /**
     * @throws \Machour\Konnect\ApiException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function callApi(string $method, string $endpoint, array $params = []): array
    {
        $domain = $this->productionMode ? 'api' : 'api.preprod';

        $request = $this->requestFactory
            ->createRequest($method, "https://$domain.konnect.network/api/v2/$endpoint")
            ->withHeader('X-Api-Key', $this->apiKey);

        if ($method === 'POST') {
            $stream = $this->streamFactory->createStream(json_encode($params));
            $request = $request->withBody($stream)->withHeader('Content-Type', 'application/json');
        }

        $response = $this->httpClient->sendRequest($request);
        $statusCode = $response->getStatusCode();

        $body = json_decode($response->getBody()->getContents(), true);

        if ($statusCode !== 200) {
            throw new ApiException($body['errors'], $response->getReasonPhrase(), $statusCode);
        }

        return $body;
    }

    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setProductionMode($production = true)
    {
        $this->productionMode = $production;
    }
}

