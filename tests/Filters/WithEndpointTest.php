<?php

namespace tests\Filters;

use BlastCloud\Hybrid\{Expectation, UsesHybrid};
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\AssertionFailedError;

class WithEndpointTest extends TestCase
{
    use UsesHybrid;

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://www.something.app']);
    }

    public function testWithEndpointPasses()
    {
        $this->hybrid->expects($this->once())
            ->withEndpoint('https://www.something.app/v1/api/companies', 'GET')
            ->willRespond(new MockResponse());

        $this->client->request('GET', '/v1/api/companies?first=argument&second=another');
    }

    public function testWithEndpointFails()
    {
        $this->hybrid->queueResponse(new MockResponse());
        $this->client->request('GET', '/v1/api/companies');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bPOST\b/");

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withEndpoint('/v1/api/users', 'POST');
        });
    }
}