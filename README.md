<p align="center"><img src="Hybrid-logo.svg" width="450"></p>
<p align="center">
    <a href="https://travis-ci.org/blastcloud/hybrid">
        <img src="https://img.shields.io/github/workflow/status/blastcloud/hybrid/run-tests?label=tests">
    </a>
    <a href="#">
        <img src="https://poser.pugx.org/blastcloud/hybrid/v/stable" />
    </a>
    <a href="https://codeclimate.com/github/blastcloud/hybrid/maintainability">
        <img src="https://api.codeclimate.com/v1/badges/1351a1b75d4bea156f66/maintainability" />
    </a>
    <a href="https://github.com/blastcloud/hybrid/blob/master/LICENSE.md">
        <img src="https://poser.pugx.org/blastcloud/hybrid/license" />
    </a>
</p>

---

> Full Documentation at [hybrid.guzzler.dev](https://hybrid.guzzler.dev)

Charge up your app or SDK with a testing library specifically for Symfony/HttpClient.

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

## Documentation

[Full Documentation](https://hybrid.guzzler.dev)

## License

Hybrid is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
