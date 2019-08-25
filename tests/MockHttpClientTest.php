<?php


namespace tests;


use BlastCloud\Hybrid\MockHttpClient;
use BlastCloud\Hybrid\UsesHybrid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\Response\MockResponse;

class MockHttpClientTest extends TestCase
{
    use UsesHybrid;

    /** @var MockHttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://www.somewhere.info/api']);
    }

    public function testExceptionThrownIfClosureYieldNonString()
    {
        $this->expectException(TransportException::class);
        $this->expectExceptionMessageRegExp("/\bmust be string\b/");

        $this->hybrid->queueResponse(new MockResponse());

        $this->client->request('GET', '/awoei', [
            'body' => function () {
                for ($i = 0; $i < 10; $i++) {
                    yield ['something' => 'value'];
                }
            }
        ]);
    }
}