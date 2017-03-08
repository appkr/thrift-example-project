<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use App\Thrift\BarMiddleware;
use App\Thrift\FooMiddleware;
use App\Thrift\ThriftResponse;
use App\Thrift\ThriftServiceHandler;
use Appkr\Thrift\Post\PostServiceProcessor;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    /**
     * Thrift 요청을 처리하고 응답합니다.
     *
     * @param Request $request
     * @param string $format
     * @return mixed
     */
    public function handle(Request $request, $format = 'json')
    {
        // 체인을 만듭니다. 사용법은 데코레이터 패턴과 비슷합니다.
        // 가장 바깥 쪽의 체인를 먼저 거칩니다.
        $middleware = new FooMiddleware(
            new BarMiddleware
        );

        $service = new PostService();
        $handler = new ThriftServiceHandler($service, $middleware);
        $processor = new PostServiceProcessor($handler);

        return ThriftResponse::make($request, $processor, $format);
    }
}
