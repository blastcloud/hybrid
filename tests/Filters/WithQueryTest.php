<?php

namespace Tests\Filters;

use Tests\ExceptionMessageRegex;
use BlastCloud\Hybrid\{UsesHybrid, Expectation};
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class WithQueryTest extends TestCase
{
    use UsesHybrid, ExceptionMessageRegex;

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
        $this->{self::$regexMethodName}("/\bExclusive: true\b/");

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/some-url', [
            'query' => ['first' => 'a-value', 'second' => 'another-value']
        ]);

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withQuery(['second' => 'another-value'], true);
        });
    }

    public function testWithQueryKey()
    {
        $this->hybrid->queueResponse(new MockResponse());

        $this->hybrid->expects($this->once())
            ->withQueryKey('first');

        $this->client->request('GET', '/some-url', [
            'query' => ['first' => 'a-value']
        ]);

        $this->expectException(AssertionFailedError::class);
        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withQueryKey('second');
        });
    }

    public function testWithQueryKeys()
    {
        $this->hybrid->expects($this->once())
            ->withQueryKeys(['second', 'first'])
            ->willRespond(new MockResponse());

        $this->client->request('GET', '/somewhere', ['query' => ['first' => 'value', 'second' => 'woeiw']]);

        $this->expectException(AssertionFailedError::class);
        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withQueryKeys(['third']);
        });
    }

    public function testWithoutQuery()
    {
        $this->hybrid->expects($this->once())
            ->withoutQuery()
            ->get('/first')
            ->willRespond(new MockResponse());

        $this->client->request('GET', '/first');

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/second', ['query' => ['item' => 'a-value']]);

        $this->expectException(AssertionFailedError::class);
        $this->hybrid->assertLast(function (Expectation $e) {
            return $e->withoutQuery();
        });
    }
}