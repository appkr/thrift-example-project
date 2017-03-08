<?php

namespace App\Thrift;

use Appkr\Thrift\Post\PostServiceProcessor;
use Illuminate\Http\Request;
use Log;
use stdClass;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Transport\TMemoryBuffer;
use Thrift\Protocol\TJSONProtocol;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TTransport;
use UnexpectedValueException;

class ThriftResponse
{
    /**
     * Thrift 요청을 처리하고 HTTP 응답을 만듭니다.
     *
     * @param Request $request
     * @param PostServiceProcessor $processor
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\Response|
     *      \Illuminate\Contracts\Routing\ResponseFactory
     */
    public static function make(
        Request $request, 
        PostServiceProcessor $processor, 
        string $format)
    {
        $thriftEncodedRequestBody = $request->getContent(false);

        Log::info(
            'thrift_encoded_request_body:'
            . PHP_EOL . $thriftEncodedRequestBody . PHP_EOL
        );

        $readTransport = new TMemoryBuffer($thriftEncodedRequestBody);
        $writeTransport = new TMemoryBuffer();

        $protocol = static::getProtocol(
            $format, 
            $readTransport, 
            $writeTransport
        );

        $readTransport->open();
        $writeTransport->open();
        
        $processor->process(
            $protocol->reader, 
            $protocol->writer
        );

        $readTransport->close();
        $writeTransport->close();

        $thriftEncodedResponseBody = $writeTransport->getBuffer();

        Log::info(
            'thrift_encoded_response_body'
            . PHP_EOL . $thriftEncodedResponseBody . PHP_EOL
        );

        return response($thriftEncodedResponseBody)
            ->header('Content-Type', 'application/x-thrift')
            ->header(
                'Access-Control-Allow-Origin', 
                $request->header('origin')
            );
    }

    /**
     * 읽기 및 쓰기 프로토콜 객체를 생성합니다.
     *
     * @param string $format
     * @param TMemoryBuffer $readTransport
     * @param TMemoryBuffer $writeTransport
     * @return stdClass
     */
    private static function getProtocol(
        string $format,
        TMemoryBuffer $readTransport,
        TMemoryBuffer $writeTransport
    )
    {
        $protocol = new stdClass;
        $protocol->reader = self::getStrategy($format, $readTransport);
        $protocol->writer = self::getStrategy($format, $writeTransport);

        return $protocol;
    }

    /**
     * Thrift 프로토콜 객체를 생성합니다.
     *
     * @param string $format
     * @param TTransport $transport
     * @return TBinaryProtocol|TCompactProtocol|TJSONProtocol
     */
    private static function getStrategy(string $format, TTransport $transport)
    {
        switch ($format) {
            case 'json':
                return new TJSONProtocol($transport);
            case 'binary':
                return new TBinaryProtocol($transport);
            case 'compact':
                return new TCompactProtocol($transport);
        }

        throw new UnexpectedValueException;    
    }
}