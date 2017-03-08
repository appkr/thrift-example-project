<?php

use Appkr\Thrift\Post\Post as ThriftPost;
use Appkr\Thrift\Post\PostServiceClient;
use Appkr\Thrift\Post\QueryFilter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TJSONProtocol;
use Thrift\Transport\THttpClient;

/**
 * @property \Appkr\Thrift\Post\PostServiceClient client
 */
class ThriftClientTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $transport = new THttpClient(
            'localhost',
            '8000',
            'api/posts'
//            'api/posts/binary'
//            'api/posts/compact'
        );

        $protocol = new TJSONProtocol($transport);
//        $protocol = new TBinaryProtocol($transport);
//        $protocol = new TCompactProtocol($transport);

        $this->client = new PostServiceClient($protocol);
    }

    public function testAll()
    {
        $queryFilter = new QueryFilter([
            'keyword' => 'Lorem',
            'sortBy' => 'id',
            'sortDirection' => 'desc'
        ]);

        $response = $this->client->all($queryFilter, 0, 10);

        print_r($response);

        if (count($response)) {
            $this->assertInstanceOf(ThriftPost::class, $response[0]);
        }
    }

    public function testFind()
    {
        $response = $this->client->find(1);

        print_r($response);

        $this->assertInstanceOf(ThriftPost::class, $response);
        $this->assertEquals($response->id, 1);
    }

    public function testStore()
    {
        $response = $this->client->store(
            new ThriftPost([
                'title' => 'foo',
                'content' => 'Lorem content'
            ])
        );

        print_r($response);

        $this->assertInstanceOf(ThriftPost::class, $response);
        $this->assertEquals($response->title, 'foo');
    }
}
