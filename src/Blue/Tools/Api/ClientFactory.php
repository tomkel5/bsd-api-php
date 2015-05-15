<?php
namespace Blue\Tools\Api;

use GuzzleHttp\Client;

class ClientFactory {

    public function createClient() {

        $client = new Client(
            [
                'message_factory' => new MessageFactory()
            ]
        );

        return $client;

    }

}