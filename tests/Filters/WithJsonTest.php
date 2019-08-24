<?php

namespace tests\Filters;

use BlastCloud\Hybrid\UsesHybrid;
use Symfony\Component\HttpClient\HttpClient;
use PHPUnit\Framework\{TestCase, AssertionFailedError};
use Symfony\Component\HttpClient\Response\MockResponse;

class WithJsonTest extends TestCase
{
    use UsesHybrid;

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'http://somehwere.com/api/']);
    }

    public function testJsonExact()
    {
        $this->hybrid->queueMany(new MockResponse(), 3);

        $form = [
            'first' => 'a value',
            'second' => 'another value'
        ];

        $this->hybrid->expects($this->atLeastOnce())
            ->withJson($form, true);

        $this->client->request('POST', '/woeij', [
            'json' => $form
        ]);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bJSON\b/");

        $this->client->request('POST', '/awoei', [
            'json' => $form + ['woeij' => 'aoiejw']
        ]);

        $this->hybrid->assertLast(function ($expect) use ($form) {
            return $expect->withJson($form, true);
        });
    }

    public function testJsonContains()
    {
        $this->hybrid->queueMany(new MockResponse(), 2);

        $nestedJson = [
            'first' => [
                'nested' => 'nested value'
            ]
        ];
        $this->hybrid->expects($this->atLeastOnce())
            ->withJson($nestedJson);

        $this->client->request('POST', '/coewiu', [
            'json' => $nestedJson
        ]);

        // Now Test Failure
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageRegExp("/\bJSON\b/");

        $this->client->request('POST', '/aweio', [
            'json' => $nestedJson
        ]);

        $this->hybrid->assertLast(function ($e) {
            return $e->withJson(['something' => 'not there']);
        });
    }
}