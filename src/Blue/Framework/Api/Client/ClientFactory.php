<?php
namespace Blue\Framework\Api\Client;

use Psr\Log\LoggerInterface;
use RuntimeException;

class ClientFactory {

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $bundlePath = '/etc/bluestate/bundles';

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function createClient($id, $secret, $url) {
        $client = new Client($id, $secret, $url);

        $client->setLogger($this->logger);

        return $client;
    }

    public function createInternalClient($clientSlug) {

        $bundleFile = "{$this->bundlePath}/$clientSlug/masterdb.json";

        if (!is_file($bundleFile)) {
            throw new RuntimeException('Could not read bundle file');
        }

        $bundle = file_get_contents($bundleFile);
        $config = json_decode($bundle, true);

        if (!$config) {
            throw new RuntimeException('Could not parse bundle file');
        }

        if (!array_key_exists('api_secret', $config)) {
            throw new RuntimeException('Could not locate API secret in bundle file');
        }

        $apiSecret = $config['api_secret'];

        $client = new Client('$internal', $apiSecret, "http://$clientSlug.bsd.net");

        return $client;
    }

    /**
     * @param string $bundlePath
     */
    public function setBundlePath($bundlePath)
    {
        $this->bundlePath = $bundlePath;
    }


}