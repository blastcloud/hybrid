<?php

namespace Tests;

use BlastCloud\Hybrid\Hybrid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

class HybridTest extends TestCase
{
    public $hybrid;

    public function setUp(): void
    {
        parent::setUp();

        $this->hybrid = new Hybrid($this);
    }

    public function testGetClient()
    {
        $res = $this->hybrid->getClient();

        $this->assertInstanceOf(MockHttpClient::class, $res);
    }
}