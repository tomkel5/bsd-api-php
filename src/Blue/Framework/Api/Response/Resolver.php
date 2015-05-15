<?php
namespace Blue\Framework\Api\Response;

use Blue\Framework\Api\Client\Client;
use Blue\FrameworkApi\Response\ReadyResponse;
use GuzzleHttp\Message\ResponseInterface;
use InvalidArgumentException;

class Resolver {

    const HTTP_STATUS_CODE_DEFERRED = 202;
    const HTTP_STATUS_CODE_DEFERRED_EMPTY = 204;
    const HTTP_STATUS_CODE_OK = 200;

    /** @var Client */
    private $client;

    function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ResponseInterface $response
     * @return Response
     */
    public function resolve(ResponseInterface $response) {

        switch ($response->getStatusCode()) {
            case self::HTTP_STATUS_CODE_OK:
                return new ReadyResponse($response);
            case self::HTTP_STATUS_CODE_DEFERRED:
            case self::HTTP_STATUS_CODE_DEFERRED_EMPTY:
                return new DeferredResponse($response, $this->client);
        }

        throw new InvalidArgumentException("Could not handle HTTP status code " . $response->getStatusCode());

    }

}