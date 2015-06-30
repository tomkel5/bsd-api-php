<?php
namespace Blue\Tools\Api;

use Blue\Tools\Api\Response\ToolsApiResponse;
use GuzzleHttp\Message\FutureResponse;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Message\ResponseInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{

    //--------------------
    // Constants
    //--------------------

    /** @var int */
    static $VERSION = 2;

    /** @var string */
    static $AUTH_TYPE = 'bsdtools_v2';

    //--------------------
    // Credentials
    //--------------------

    /** @var string */
    private $id;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $secret;

    //--------------------
    // Configuration
    //--------------------

    /** @var int */
    private $deferredResultMaxAttempts = 20;

    /** @var int */
    private $deferredResultInterval = 5;

    /** @var bool */
    private $autoResolve = true;

    //--------------------
    // Other internals
    //--------------------

    /** @var LoggerInterface */
    private $logger;

    /** @var GuzzleClient */
    private $guzzleClient;


    /**
     * @param string $id
     * @param string $secret
     * @param string $url
     */
    public function __construct($id, $secret, $url)
    {
        $this->logger = new NullLogger();

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

        $this->guzzleClient = new GuzzleClient(
            [
                'message_factory' => new MessageFactory()
            ]
        );
    }

    /**
     * Execute a GET request against the API
     *
     * @param string $apiPath
     * @param array $queryParams
     * @return ResponseInterface
     */
    public function get($apiPath, $queryParams = [])
    {
        $response = $this->guzzleClient->get(
            $this->baseUrl . $apiPath,
            [
                'query' => $queryParams,
                'future' => false,
                'auth' => [
                    $this->id,
                    $this->secret,
                    self::$AUTH_TYPE
                ],
            ]
        );

        $toolsApiResponse = new ToolsApiResponse($response);

        return ($this->autoResolve)
            ? $this->resolveResponse($toolsApiResponse)
            : $toolsApiResponse;
    }


    /**
     * Execute a POST request against the API
     *
     * @param $apiPath
     * @param array $queryParams
     * @param string $data
     * @return ResponseInterface
     */
    public function post($apiPath, $queryParams = [], $data = '')
    {

        $response = $this->guzzleClient->post(
            $this->baseUrl . $apiPath,
            [
                'query' => $queryParams,
                'body' => $data,
                'future' => false,
                'auth' => [
                    $this->id,
                    $this->secret,
                    self::$AUTH_TYPE
                ],
            ]
        );

        $toolsApiResponse = new ToolsApiResponse($response);

        return ($this->autoResolve)
            ? $this->resolveResponse($toolsApiResponse)
            : $toolsApiResponse;
    }


    /**
     * Resolve the response (if it was deferred)
     *
     * @param ToolsApiResponse $response
     * @return FutureResponse|Response|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function resolveResponse(ToolsApiResponse $response) {

        if ($response->isDeferred()) {
            $key = $response->getDeferredKey();

                return $this->resolveByDeferredKey($key);
            }

        // If the request was not deferred, then return as-is
        return $response;
    }

    /**
     * Resolve a response from a deferred key
     *
     * @param $key
     * @return ToolsApiResponse
     */
    public function resolveByDeferredKey($key) {

        $attempts = $this->deferredResultMaxAttempts;

        while($attempts > 0) {
            /** @var ResponseInterface $deferredResponse */
            $deferredResponse = $this->guzzleClient->get(
                $this->baseUrl . "get_deferred_results",
                [
                    'auth' => [
                        $this->id,
                        $this->secret,
                        self::$AUTH_TYPE
                    ],
                    'future' => false,
                    'query' => [
                        'deferred_id' => $key
                    ]
                ]
            );

            $deferredToolsApiResponse = new ToolsApiResponse($deferredResponse);

            if (!$deferredToolsApiResponse->isDeferred()) {
                return $deferredToolsApiResponse;
            }

            sleep($this->deferredResultInterval);
            $attempts--;
        }

        throw new RuntimeException("Could not load deferred response after {$this->deferredResultMaxAttempts} attempts");
    }


    /**
     * @param int $deferredResultMaxAttempts
     */
    public function setDeferredResultMaxAttempts($deferredResultMaxAttempts)
    {
        $this->deferredResultMaxAttempts = $deferredResultMaxAttempts;
    }


    /**
     * @param int $deferredResultInterval
     */
    public function setDeferredResultInterval($deferredResultInterval)
    {
        $this->deferredResultInterval = $deferredResultInterval;
    }


    /**
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }


    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Tells the client whether it should automatically resolve deferred responses
     *
     * @param bool $autoResolve
     */
    public function setAutoResolve($autoResolve)
    {
        $this->autoResolve = $autoResolve;
    }
}