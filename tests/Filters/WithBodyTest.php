<?php


namespace Tests\Filters;

use Tests\ExceptionMessageRegex;
use BlastCloud\Hybrid\{MockHttpClient, UsesHybrid, Expectation};
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class WithBodyTest extends TestCase
{
    use UsesHybrid, ExceptionMessageRegex;

    public HttpClient|MockHttpClient $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://www.example.com']);
    }

    public function testWithBodyExclusive()
    {
        $body = ['something' => 'some value'];

        $this->hybrid->queueMany(new MockResponse(''), 2);

        $this->client->request('POST', '/url', [
            'json' => $body
        ]);

        $this->client->request('POST', '/aowe', [
            'body' => json_encode($body) . 'something extra'
        ]);

        $this->hybrid->assertFirst(function (Expectation $e) use ($body) {
            return $e->withBody(json_encode($body), true);
        });

        $this->hybrid->assertNotLast(function (Expectation $e) use ($body) {
            return $e->withBody(json_encode($body), true);
        });
    }

    public function testWithBodyContains()
    {
        $body = 'Some long nasty string to test things against.';

        $this->hybrid->queueResponse(new MockResponse('the response body', [
            'status_code' => 204
        ]));

        $this->client->request('POST', '/awoiue', ['body' => $body]);

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withBody('nasty string');
        });
        $this->hybrid->assertNotFirst(function (Expectation $e) {
            return $e->withBody('fantastic fantastic');
        });
    }

    public function testWithBodyError()
    {
        $this->expectException(AssertionFailedError::class);
        $this->{self::$regexMethodName}("/\bBody:\b/");
        $this->{self::$regexMethodName}("/\bhello\b/");

        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/aowei');

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withBody('hello');
        });
    }
}