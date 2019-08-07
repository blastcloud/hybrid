<?php

namespace tests\Filters;

use BlastCloud\Hybrid\UsesHybrid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

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
}