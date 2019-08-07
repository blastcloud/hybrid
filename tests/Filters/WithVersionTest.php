<?php

namespace tests\Filters;

use BlastCloud\Hybrid\UsesHybrid;
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class WithVersionTest extends TestCase
{
    use UsesHybrid;

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://amazing-url.org']);
    }

    public function testWithProtocol()
    {
        $this->hybrid->expects($this->once())
            ->withVersion(2.0);

        $this->hybrid->queueResponse(new MockResponse(''));

        $this->client->request('GET', '/aoweij', [
            'http_version' => 2.0
        ]);
    }

    public function testWithBodyError()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bVersion:\b/");
        $this->expectExceptionMessageRegExp("/\b2\b/");

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/aowei');

        $this->hybrid->assertFirst(function ($e) {
            return $e->withVersion(2.0);
        });
    }
}