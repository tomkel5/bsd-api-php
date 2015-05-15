<?php
namespace Blue\Tools\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Event\ProgressEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Message\FutureResponse;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Ring\Client\Middleware;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Ring\Future\FutureArray;
use GuzzleHttp\Ring\Future\FutureValue;
use PHPUnit_Framework_TestCase;
use React\Promise\Deferred;

class ClientFactoryTest extends PHPUnit_Framework_TestCase {

    public function testFactory() {

        $factory = new ClientFactory();

        $client = new Client(
            [
                'message_factory' => new MessageFactory(),
            ]
        );


        $name = 'cons_group_' . time();

        $addResponse = $client->get(
            "https://tktest01.bsd.net/page/api/cons_group/add_constituent_groups",
            [
                'auth' => [
                    'all',
                    '97412986223e63438fa55748f07641140a5300fc',
                    'blue'
                ],
                'body' => '<?xml version="1.0" encoding="utf-8"?>
                <api>
                    <cons_group>
                        <name>'.$name.'</name>
                    </cons_group>
                </api>
                ',
                'future' => true,
            ]
        );



        $addResponse->wait();

//        $deferred = new Deferred();
//        $promise = $deferred->promise();
//
//        $futureValue = new FutureValue(
//            $promise,
//            function() use ($deferred) {
//                $i = 0;
//
//                $deferred->resolve();
//            }
//        );
//
//        $response = $futureValue->wait();


        $content = $addResponse->getBody()->getContents();

    $i = 0;

        $xml = new \SimpleXMLElement($content);
        $consGroupId = (int) $xml->cons_group['id'];














        $deleteResponse = $client->get(
            "https://tktest01.bsd.net/page/api/cons_group/delete_constituent_groups",
            [
                'auth' => [
                    'all',
                    '97412986223e63438fa55748f07641140a5300fc',
                    'blue'
                ],
                'query' => [
                    'cons_group_ids' => $consGroupId
                ],
                'future' => true,
            ]
        );






        $deleteResponse->promise()->then(
            function(Response $response) use ($client) {
                if ($response->getStatusCode() == 202) {

                    $key = $response->getBody()->getContents();

                    $attempts = 20;

                    while($attempts > 0) {
                        $deferredResponse = $client->get(
                            "https://tktest01.bsd.net/page/api/get_deferred_results",
                            [
                                'auth' => [
                                    'all',
                                    '97412986223e63438fa55748f07641140a5300fc',
                                    'blue'
                                ],
                                'future' => false,
                                'query' => [
                                    'deferred_id' => $key
                                ]
                            ]
                        );

                        if ($deferredResponse->getStatusCode() != 202) {
                            return $deferredResponse;
                        }

                        sleep(5);
                        $attempts++;
                    }



                }
            }
        );





        $deleteResponse->wait();


        $this->assertNotNull($client);


    }

}