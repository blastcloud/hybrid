---
title: Hybrid | Official Documentation
lang: en-US
footer: MIT Licensed | Copyright © 2019-present Adam Kelso
layout: HomeLayout
---


Charge up your app or SDK with a testing library specifically for Symfony/HttpClient. Hybrid covers the process of setting up a mock handler, recording history of requests, and provides several convenience methods for creating expectations and assertions on that history.

## Installation

```bash
composer require --dev --prefer-dist blastcloud/hybrid
```

## Example Usage

```php
<?php

use BlastCloud\Hybrid\{Expectation, UsesHybrid};
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    use UsesHybrid;

    public $classToTest;

    public function setUp(): void
    {
        parent::setUp();
    
        $client = $this->hybrid->getClient([
            /* Any configs for a client */
            "base_uri" => "https://example.com/api"
        ]);
        
        // You can then inject this client object into your code or IOC container.
        $this->classToTest = new ClassToTest($client);
    }

    public function testSomethingWithExpectations()
    {
        $this->hybrid->expects($this->once())
            ->post("/some-url")
            ->withHeader("X-Authorization", "some-key")
            ->willRespond(new MockResponse("Some body"));
    
        $this->classToTest->someMethod();
    }

    public function testSomethingWithAssertions()
    {
        $this->hybrid->queueResponse(
            new MockResponse(null, ['http_code' => 204]),
            new \Exception("Some message"),
            // any needed responses to return from the client.
        );
    
        $this->classToTest->someMethod();
        // ... Some other number of calls
    
        $this->hybrid->assertAll(function (Expectation $expect) {
            return $expect->withHeader("Authorization", "some-key");
        });
    }
}
```

---

<p align="center">MIT Licensed | Copyright © 2019-present Adam Kelso</p>
