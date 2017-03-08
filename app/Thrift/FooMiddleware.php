<?php

namespace App\Thrift;

use Appkr\Thrift\Errors\ErrorCode;
use Appkr\Thrift\Errors\SystemException;
use Appkr\Thrift\Errors\UserException;
use Exception;
use Log;

class FooMiddleware extends ThriftMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle($service, $method, $arguments)
    {
        try {
            Log::info(
                sprintf('handling_thrift_request_at %s:%d:' . PHP_EOL, __CLASS__, __LINE__),
                func_get_args()
            );

            return $this->next($service, $method, $arguments);
        } catch (SystemException $e) {
            // 여기서 예외를 처리합니다.
            throw $e;
        } catch (UserException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::debug(sprintf(
                "%s \n\n%s \n%s:%d \n\n%s",
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ));

            throw new SystemException([
                'code' => ErrorCode::INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
}