<?php
namespace Blue\Framework\Api\Response;

use Blue\Framework\Api\Client\Client;
use GuzzleHttp\Message\ResponseInterface;
use RuntimeException;

class DeferredResponse implements Response {

    /** @var ResponseInterface */
    private $response;

    /** @var Client */
    private $client;

    public function __construct(ResponseInterface $response, Client $client) {
        $this->response = $response;
        $this->client = $client;
    }

    public function getContent() {

        $attempts = 0;

        $deferredKey = $this->response->getBody()->getContents();

        while ($attempts++ < $this->client->getDeferredResultMaxAttempts()) {
            $response = $this->client->get('get_deferred_results', ['deferred_id' => $deferredKey]);

            if ($response->getStatusCode() == Resolver::HTTP_STATUS_CODE_OK) {
                return $response->getContent();
            }

            sleep($this->client->getDeferredResultInterval());

        }

        throw new RuntimeException("Could not get deferred result after $attempts attempts");

    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }


}