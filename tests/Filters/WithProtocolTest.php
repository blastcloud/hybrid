<?php

namespace Tests\Filters;

use tests\ExceptionMessageRegex;
use BlastCloud\Hybrid\{UsesHybrid, Expectation};
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class WithProtocolTest extends TestCase
{
    use UsesHybrid, ExceptionMessageRegex;

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
            ->withProtocol(2.0);

        $this->hybrid->queueResponse(new MockResponse(''));

        $this->client->request('GET', '/aoweij', [
            'http_version' => 2.0
        ]);
    }

    public function testWithBodyError()
    {
        $this->expectException(AssertionFailedError::class);
        $this->{self::$regexMethodName}("/\bProtocol:\b/");
        $this->{self::$regexMethodName}("/\b2\b/");

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/aowei');

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withProtocol(2.0);
        });
    }
}