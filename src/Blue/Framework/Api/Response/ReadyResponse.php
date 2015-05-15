<?php
namespace Blue\FrameworkApi\Response;

use Blue\Framework\Api\Response\Response;
use GuzzleHttp\Message\ResponseInterface;

class ReadyResponse implements Response {

    /** @var ResponseInterface */
    private $response;

    public function __construct(ResponseInterface $response) {
        $this->response = $response;
    }

    public function getContent() {
        return $this->response->getBody()->getContents();
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }


}