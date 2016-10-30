<?php

namespace App\Thrift;

class BarMiddleware extends ThriftMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle($service, $method, $arguments)
    {
        // Handle the request
        \Log::info('handling thrift request', [func_get_args(), __METHOD__]);

        return $this->next($service, $method, $arguments);
    }
}