<?php

namespace App\Thrift;

/**
 * @property  service
 * @property \App\Thrift\ThriftMiddleware|null middleware
 */
class ThriftServiceHandler
{
    /**
     * ThriftServiceHandler constructor.
     *
     * @param $service
     * @param ThriftMiddleware|null $middleware
     */
    public function __construct($service, ThriftMiddleware $middleware = null)
    {
        $this->service = $service;
        $this->middleware = $middleware;
    }

    /**
     * 이 클래스에 없는 메서드를 호출해줍니다.
     * 이 메서드는 ServiceProcessor가 최초로 호출해 줍니다.
     * 이 때 인자로 $method = 'all', $arguments = [<object>QueryFilter, 0, 10]를 받습니다.
     * 즉, 체인을 시작합니다.
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($this->middleware) {
            return $this->middleware->handle($this->service, $method, $arguments);
        }

        return call_user_func_array([$this->service, $method], $arguments);
    }
}