<?php

namespace tests\Filters;

use BlastCloud\Hybrid\Hybrid;
use BlastCloud\Hybrid\UsesHybrid;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use BlastCloud\Hybrid\Expectation;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class WithOptionTest extends TestCase
{
    use UsesHybrid;

    /**
     * This constant is here just so we can have a test of overwriting
     * the engine name. Using the copied / pasted test from Guzzler.
     */
    CONST ENGINE_NAME = 'guzzler';

    /** @var MockHttpClient */
    public $client;

    /** @var Hybrid */
    public $guzzler;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->guzzler->getClient();
    }

    public function testWithOptions()
    {
        $this->guzzler->queueMany(new MockResponse(), 2);

        $this->client->request('GET', 'http://www.somewhere.org/woewij', ['stream' => true]);

        $this->guzzler->assertFirst(function (Expectation $e) {
            return $e->withOption('stream', true);
        });

        $options = ['verify' => false, 'allow_redirects' => false];
        $expectation = $this->guzzler->expects($this->once());

        $this->assertInstanceOf(Expectation::class, $expectation);

        $expectation->withOptions($options);

        $this->client->request('GET', 'http://www.somewhere.org/woei', $options);
    }

    public function testWithOptionError()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bOptions\b/");

        $this->guzzler->queueResponse(new MockResponse());
        $this->client->request('GET', 'http://www.somewhere.org/aowei');

        $this->guzzler->assertFirst(function (Expectation $e) {
            return $e->withOptions(['something' => 'not']);
        });
    }
}