<?php

namespace App\Thrift;

/**
 * Class ThriftMiddleware
 * @package App\Thrift
 */
abstract class ThriftMiddleware
{
    /**
     * @var null|ThriftMiddleware
     */
    protected $successor = null;

    /**
     * ThriftMiddleware constructor.
     *
     * @param ThriftMiddleware|null $successor
     */
    public function __construct(ThriftMiddleware $successor = null)
    {
        $this->successor = $successor;
    }

    /**
     * Thrift Request를 처리 합니다.
     *
     * @param $service
     * @param string $method
     * @param mixed $arguments
     * @return mixed
     */
    abstract public function handle($service, $method, $arguments);

    /**
     * successor에게 Thrift Request를 넘깁니다.
     *
     * @param $service
     * @param string $method
     * @param mixed $arguments
     * @return mixed
     */
    public function next($service, $method, $arguments)
    {
        if ($this->successor) {
            return $this->successor->handle($service, $method, $arguments);
        }

        // successor가 없으면 최종으로 Thrift 메서드를 호출합니다.
        return call_user_func_array([$service, $method], $arguments);
    }
}