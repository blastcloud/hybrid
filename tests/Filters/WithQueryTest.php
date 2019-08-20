<?php

namespace tests\Filters;

use BlastCloud\Hybrid\{UsesHybrid, Expectation};
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class WithQueryTest extends TestCase
{
    use UsesHybrid;

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://www.application.cloud/api']);
    }

    public function testWithQueryPass()
    {
        $this->hybrid->queueResponse(new MockResponse());

        $this->hybrid->expects($this->once())
            ->withQuery([
                'second' => 'another-value'
            ]);

        $this->client->request('GET', '/some-url', [
            'query' => ['first' => 'a-value', 'second' => 'another-value']
        ]);
    }

    public function testWithQueryFails()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bExclusive: true\b/");

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/some-url', [
            'query' => ['first' => 'a-value', 'second' => 'another-value']
        ]);

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withQuery(['second' => 'another-value'], true);
        });
    }
}