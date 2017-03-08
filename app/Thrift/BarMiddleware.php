<?php

namespace App\Thrift;

use Log;

class BarMiddleware extends ThriftMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle($service, $method, $arguments)
    {
        Log::info(
            sprintf('handling_thrift_request_at %s:%d:' . PHP_EOL, __CLASS__, __LINE__),
            func_get_args()
        );

        return $this->next($service, $method, $arguments);
    }
}