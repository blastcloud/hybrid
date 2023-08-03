---
lang: en-US
title: Getting Started | Hybrid
---

# Getting Started

::: tip Be Aware
The Symfony\HttpClient component is currently considered an "experimental feature". The underlying library may change in the future before it reaches stable. Please see the [official documentation](https://symfony.com/doc/current/components/http_client.html) for the latest.
:::

## Requirements

1. PHP 7.2+
2. Symfony\HttpClient 4.3.1+
3. PHPUnit 8.2+ & 9

## Installation

Add the dependency to your *composer.json* file.

```bash
composer require --dev --prefer-dist blastcloud/hybrid
```

Add the `BlastCloud\Hybrid\UsesHybrid` trait to your test class.

```php
use BlastCloud\Hybrid\UsesHybrid;

class SomeTest extends TestCase
{
    use UsesHybrid;
```

This trait wires up a class variable named `hybrid`. Inside that object the necessary history and mock handlers for HttpClient are instantiated and saved. You can customize the `Client` object however you like by passing in any options you would pass to a normal `MockHttpClient` in the `getClient()` method.

### getClient(array $options = [])

The `getClient` method returns a new instance of the `HttpClient` class and adds any options you like to it’s constructor. Adding extra options is **not** required.

```php
$client = $this->hybrid->getClient([
    "base_uri" => "http://some-url.com/api/v2",
    // ... Any other configurations
]);
```

## Custom Engine Name

Hybrid allows you to customize the variable name of the engine, if you prefer to not use "hybrid". To use a custom name, add a constant to the class called `ENGINE_NAME` with the value set to the variable name you'd prefer.

```php
use BlastCloud\Hybrid\UsesHybrid;

class SomeTest extends TestCase
{
    use UsesHybrid;

    public $client;
    
    // Here we define what we want the engine name to be.
    const ENGINE_NAME = 'engine';

    public function setUp(): void
    {
        parent::setUp();

        // Here, $this->hybrid has been renamed
        // to $this->engine
        $this->client = $this->engine->getClient([
            'base_uri' => 'https://some-url.com/api/v2'
        ]);
    }

    public function testSomething()
    {
        $this->engine->expects($this->once())
            ->get('/some/api/url');
        
        // ...
    }
}
```

The main benefit of using a custom engine name is to abstract as much code as possible. Though it's not likely you'll have a conflicting variable named "hybrid", it's a possibility that is covered.