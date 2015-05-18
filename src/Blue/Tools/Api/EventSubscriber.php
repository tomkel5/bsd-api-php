<?php
namespace Blue\Tools\Api;

use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Event\EmitterInterface;
use GuzzleHttp\Event\EventInterface;
use GuzzleHttp\Event\SubscriberInterface;

class EventEmitter implements  SubscriberInterface {
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The returned array keys MUST map to an event name. Each array value
     * MUST be an array in which the first element is the name of a function
     * on the EventSubscriber OR an array of arrays in the aforementioned
     * format. The second element in the array is optional, and if specified,
     * designates the event priority.
     *
     * For example, the following are all valid:
     *
     *  - ['eventName' => ['methodName']]
     *  - ['eventName' => ['methodName', $priority]]
     *  - ['eventName' => [['methodName'], ['otherMethod']]
     *  - ['eventName' => [['methodName'], ['otherMethod', $priority]]
     *  - ['eventName' => [['methodName', $priority], ['otherMethod', $priority]]
     *
     * @return array
     */
    public function getEvents()
    {
        return [
            'complete', function() {
                $i = 0;

            }
        ];
    }


}