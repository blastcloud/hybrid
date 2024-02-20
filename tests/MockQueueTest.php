<?php

namespace Tests;

use BlastCloud\Hybrid\MockQueue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class MockQueueTest extends TestCase
{
    /** @var MockHttpClient */
    public $client;

    public $history = [];

    /** @var MockQueue */
    public $queue;

    public function setUp(): void
    {
        parent::setUp();

        $this->queue = new MockQueue($this->history);
        $this->client = new MockHttpClient($this->queue, 'https://www.example.com/api/');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->history = [];
    }

    public function testCount()
    {
        $this->assertEquals(0, $this->queue->count());

        $this->queue->append('anything');
        $this->assertEquals(1, $this->queue->count());

        $this->queue->append('something else');
        $this->assertEquals(2, $this->queue->count());
    }

    public function testInvocation()
    {
        $body = 'Hello, world!';
        $mock = new MockResponse($body);
        $mock2 = new MockResponse('some body', [
            'status_code' => 404
        ]);

        $this->queue->append($mock);
        $this->queue->append($mock2);

        $response = $this->client->request('GET', 'https://www.somewhere.com/api/v2');
        $this->assertEquals(1, $this->queue->count());

        $response2 = $this->client->request('POST', 'https://www.somewhere.com/api/v2/different', [
            'body' => $body
        ]);
        $this->assertEquals(0, $this->queue->count());

        $this->assertEquals($response->getContent(), $body);
        $this->assertEquals('GET', $response->getInfo()['http_method']);

        $this->assertEquals('POST', $response2->getInfo()['http_method']);
        $this->assertEquals($body, $this->history[1]['request']['body']);
    }

    public function testThrowExceptionForEmptyQueue()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage("Mock queue is empty");

        $this->client->request('GET', 'https://www.example.com');
    }

    public function testOptionPassingAndMerging()
    {
        $options = [
            'something' => 'value',
            'another' => 'shouldBeReplaced'
        ];

        $this->queue = new MockQueue($this->history, $options);
        $this->client = new MockHttpClient($this->queue);

        $this->queue->append(new MockResponse());
        $this->client->request('GET', 'https://www.somewhere.com/api/v2', [
            'another' => $another = 'overwritten'
        ]);

        $this->assertArrayHasKey('options', $this->history[0]);
        $this->assertEquals('value', $this->history[0]['options']['something']);
        $this->assertEquals($another, $this->history[0]['options']['another']);
    }

    public function testIfGivenAnExceptionThrowIt()
    {
        $message = 'My special message on the weird exception!!';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->queue->append(new \InvalidArgumentException($message));

        $this->client->request('GET', 'anywhere');
    }
}