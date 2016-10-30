<?php

namespace App\Thrift;

use Illuminate\Http\Request;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Transport\TMemoryBuffer;
use Thrift\Protocol\TJSONProtocol;
use Thrift\Protocol\TBinaryProtocol;
use UnexpectedValueException;

class ThriftResponse
{
    public static function make(Request $request, $processor, $format)
    {
        $readTransport = new TMemoryBuffer($request->getContent(false));
        $writeTransport = new TMemoryBuffer();

        switch ($format) {
            case 'json':
                $readProtocol = new TJSONProtocol($readTransport);
                $writeProtocol = new TJSONProtocol($writeTransport);
                break;
            case 'binary':
                $readProtocol = new TBinaryProtocol($readTransport);
                $writeProtocol = new TBinaryProtocol($writeTransport);
                break;
            case 'compact':
                $readProtocol = new TCompactProtocol($readTransport);
                $writeProtocol = new TCompactProtocol($writeTransport);
                break;
            default:
                throw new UnexpectedValueException;
        }

        $readTransport->open();
        $writeTransport->open();
        $processor->process($readProtocol, $writeProtocol);
        $readTransport->close();
        $writeTransport->close();

        $content = $writeTransport->getBuffer();

        return response($content)
            ->header('Content-Type', 'application/x-thrift');
//            ->header('Access-Control-Allow-Origin', $request->header('origin'));
    }
}