<?php
namespace Blue\Tools\Api;

use GuzzleHttp\Message\FutureResponse;
use GuzzleHttp\Message\MessageFactoryInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Ring\Future\FutureInterface;
use GuzzleHttp\Url;
use React\Promise\PromiseInterface;

class Future implements FutureInterface {
    /**
     * Returns the result of the future either from cache or by blocking until
     * it is complete.
     *
     * This method must block until the future has a result or is cancelled.
     * Throwing an exception in the wait() method will mark the future as
     * realized and will throw the exception each time wait() is called.
     * Throwing an instance of GuzzleHttp\Ring\CancelledException will mark
     * the future as realized, will not throw immediately, but will throw the
     * exception if the future's wait() method is called again.
     *
     * @return mixed
     */
    public function wait()
    {
        // TODO: Implement wait() method.
    }

    /**
     * Cancels the future, if possible.
     */
    public function cancel()
    {
        // TODO: Implement cancel() method.
    }

    /**
     * @return PromiseInterface
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null)
    {
        // TODO: Implement then() method.
    }

    /**
     * @return PromiseInterface
     */
    public function promise()
    {
        // TODO: Implement promise() method.
    }


}