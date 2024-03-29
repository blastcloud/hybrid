<?php

namespace Tests\Filters;

use BlastCloud\Hybrid\{Expectation, Filters\WithEndpoint, UsesHybrid};
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\AssertionFailedError;
use Tests\ExceptionMessageRegex;

class WithEndpointTest extends TestCase
{
    use UsesHybrid, ExceptionMessageRegex;

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
        $this->{self::$regexMethodName}("/\bPOST\b/");

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withEndpoint('/v1/api/users', 'POST');
        });
    }

    public function testMacrosForVerbs()
    {
        foreach (WithEndpoint::VERBS as $verb) {
            $method = strtolower($verb);
            $this->hybrid->expects($this->once())
                ->{$method}('/url-for-'.$method.'ing')
                ->will(new MockResponse());

            $this->client->request($verb, '/url-for-'.$method.'ing', [
                'json' => ['something' => 'value']
            ]);
        }
    }
}