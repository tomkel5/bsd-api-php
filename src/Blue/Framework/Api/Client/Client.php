<?php
namespace Blue\Framework\Api\Client;

use Blue\Framework\Api\Response\Resolver;
use Blue\Framework\Api\Response\Response;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Client {

    /** @var int */
    static $VERSION = 2;


    /** @var string */
    private $id;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $secret;

    /** @var int */
    private $deferredResultMaxAttempts = 20;

    /** @var int */
    private $deferredResultInterval = 5;

    /** @var LoggerInterface */
    private $logger;

    /** @var Resolver */
    private $responseResolver;

    /** @var \GuzzleHttp\Client */
    private $guzzleClient;


    public function __construct($id, $secret, $url) {

        $this->logger = new NullLogger();
        $this->responseResolver = new Resolver($this);
        $this->guzzleClient = new \GuzzleHttp\Client();

        if (!strlen($id) || !strlen($secret)) {
            throw new InvalidArgumentException('api_id and api_secret must both be provided');
        }

        $validatedUrl = filter_var($url, FILTER_VALIDATE_URL);
        if (!$validatedUrl) {
            throw new InvalidArgumentException($url . ' is not a valid URL');
        }

        $this->id = $id;
        $this->secret = $secret;
        $this->baseUrl = $validatedUrl . '/page/api/';
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Execute a GET request against the API
     *
     * @param string $apiPath
     * @param array $queryParams
     * @return Response
     */
    public function get($apiPath, $queryParams = []) {

        $fullUrl = $this->baseUrl . $apiPath;

        $apiQueryParams = $this->getApiQueryParams($fullUrl, $queryParams);

        $queryParams = array_merge($queryParams, $apiQueryParams);

        $fullUrl .= '?' . http_build_query($queryParams);

        $response = $this->guzzleClient->get($fullUrl);

        return $this->responseResolver->resolve($response);

    }

    /**
     * Execute a POST request against the API
     *
     * @param $apiPath
     * @param array $queryParams
     * @param string $data
     * @return Response
     */
    public function post($apiPath, $queryParams = [], $data = '') {

        $fullUrl = $this->baseUrl . $apiPath;

        $apiQueryParams = $this->getApiQueryParams($fullUrl, $queryParams);

        $queryParams = array_merge($queryParams, $apiQueryParams);

        $fullUrl .= '?' . http_build_query($queryParams);

        $response = $this->guzzleClient->post(
            $fullUrl,
            [
                'body' => $data
            ]
        );

        return $this->responseResolver->resolve($response);

    }


    protected function getApiQueryParams($url, $queryParams) {
        $params = [];

        $params['api_id'] = $this->id;

        $params['api_ts'] =
            (array_key_exists('api_ts', $queryParams))
                ? $queryParams['api_ts']
                : time();

        $params['api_ver'] = self::$VERSION;

        $params['api_mac'] = $this->generateMac($url, $params);

        return $params;
    }

    protected function generateMac($url, $query)
    {
        // break URL into parts to get the path
        $urlParts = parse_url($url);

        // trim double slashes in the path
        if (substr($urlParts['path'], 0, 2) == '//') {
            $urlParts['path'] = substr($urlParts['path'], 1);
        }

        // build query string from given parameters
        $queryString = urldecode(http_build_query($query));

        // combine strings to build the signing string
        $signingString = $query['api_id'] . "\n" .
            $query['api_ts'] . "\n" .
            $urlParts['path'] . "\n" .
            $queryString;

        $mac = hash_hmac('sha1', $signingString, $this->secret);
        $this->logger->debug("Generated hash: $mac");


        return $mac;
    }

    /**
     * @return int
     */
    public function getDeferredResultMaxAttempts()
    {
        return $this->deferredResultMaxAttempts;
    }

    /**
     * @param int $deferredResultMaxAttempts
     */
    public function setDeferredResultMaxAttempts($deferredResultMaxAttempts)
    {
        $this->deferredResultMaxAttempts = $deferredResultMaxAttempts;
    }

    /**
     * @return int
     */
    public function getDeferredResultInterval()
    {
        return $this->deferredResultInterval;
    }

    /**
     * @param int $deferredResultInterval
     */
    public function setDeferredResultInterval($deferredResultInterval)
    {
        $this->deferredResultInterval = $deferredResultInterval;
    }

    /**
     * @param \GuzzleHttp\Client $guzzleClient
     */
    public function setGuzzleClient(\GuzzleHttp\Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

}