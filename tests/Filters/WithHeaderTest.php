<?php

namespace tests\Filters;

use BlastCloud\Hybrid\{Expectation, UsesHybrid};
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\HttpClient;

class WithHeaderTest extends TestCase
{
    use UsesHybrid;

    /** @var HttpClient  */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://spectacular.spectacular.cloud']);
    }

    public function testWithHeaders()
    {
        $headers = [
            'X-Something' => 'Special',
            'host' => 'example.com',
            'Content-Type' => 'application/json'
        ];

        $this->hybrid->queueResponse(new MockResponse());

        $this->hybrid->expects($this->once())
            ->withHeader('Auth', 'Fantastic')
            ->withHeaders($headers);

        $this->client->request('GET', '/url', [
            'headers' => $headers + ['Auth' => 'Fantastic']
        ]);
    }

    public function testWithHeadersFail()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bHeaders\b/");

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/url', [
            'headers' => ['auth' => 'some-token']
        ]);

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withHeaders([
                'something' => 'a value'
            ]);
        });
    }
}