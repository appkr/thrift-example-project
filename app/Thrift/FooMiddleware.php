<?php

namespace App\Thrift;

use Appkr\Thrift\Errors\ErrorCode;
use Appkr\Thrift\Errors\SystemException;
use Appkr\Thrift\Errors\UserException;
use Log;

class FooMiddleware extends ThriftMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle($service, $method, $arguments)
    {
        try {
            // Handle the request
            Log::info('handling thrift request', [func_get_args(), __METHOD__]);

            return $this->next($service, $method, $arguments);
        } catch (SystemException $e) {
            // Handle exception
            throw $e;
        } catch (UserException $e) {
            // Handle exception
            throw $e;
        } catch (\Exception $e) {
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