---
lang: en-US
title: Why | Hybrid
---

# Why This Library?

This project is a port of the [Guzzler](https://guzzler.dev) library, and is intended to service the Symfony community. Guzzler started as a personal itch to have tests for working with Guzzle that were more descriptive and documenting of what they were testing. The same is now possible for HttpClient too.

## Recording History

HttpClient allows us to insert mock responses, but we're on our own to build out a way to record the history of requests. Though mocking responses is highly necessary to building API integrations, verifying the requests sent out are just as important. By using a mock client built by Hybrid, the history of the requests are recorded for later verification.

```php
public $client;

public function setUp(): void
{
    parent::setUp();

    // The mock client returned automatically records all requests made in your test
    $this->client = $this->hybrid->getClient([
        'base_uri' => 'https://some-api-url.com/api/v3'
    ]);

    $this->codeToTest = new SomeClass($this->client);
}

public function testSomething()
{
    // This response will now be returned the first time a request is made to the
    // client that we injected into our class in the setUp method.
    $this->hybrid->queueResponse(new MockResponse('Some Body'));

    // ... Other code
}
```

## Treat Your Expectations Like PHPUnit Mocks

If you were to record the history yourself, you still need a way to go into each request to verify it was made as intended.

```php
// Verify it was a POST
$this->assertEquals('POST', $history[0]['request']['method']);

// Verify it was the correct URL
$url = parse_url($history[0]['request']['url']);
$this->assertEquals("/v3/company/{$this->companyId}/bill", $url['path']);

// Verify the request was a JSON request and that the
// body contains the required JSON data
$this->assertEquals("content-type: application/json", $history[0]['request']['normalized_headers']['content-type');
$body = json_decode($history[0]['request']['body'], true);
$this->assertEquals('AccountBasedExpenseLineDetail', $body['some-nested-place']['DetailType']);
$this->assertEquals(200.0, $body['some-nested-place']['Amount']);
$this->assertEquals($bill->id, $body['some-nested-place']['Id'];
```

Instead of tightly coupling our tests to HttpClient's configuration, it's helpful to say exactly what we are testing for, and it would be nice to copy PHPUnit’s way of saying _“we want to ensure {x} happens {y} number of times.”_

```php
// PHPUnit Mock and Invokables Syntax
$mock->expects($this->once())
    ->method(/* some method name */)
    ->with(/* some argument */)
    ->willReturn(/* some result */);
```

Hybrid's chainable `Expectation`s allow us to specify every aspect of the request we care about.

```php
$this->hybrid->expects($this->atLeast(1))
    ->post("/v3/company/{$this->companyId}/bill")
    ->withJson([
        'DetailType' => 'AccountBasedExpenseLineDetail',
        'Amount' => 200.0,
        'Id' => $bill->id
    ])
    ->willRespond(new MockResponse(
        file_get_contents(__DIR__.'/quickbook-stubs/bill-created.json'),
        ['http_code' => 201]
    ));
```

## Verify All or Part of Your Requests

Hybrid provides several helper assertions that allow you to create expectations around either the entirety of your requests, or just a specific subset. Now, you don't have to iterate through all your history items by hand.

```php
// Without Hybrid
foreach ($history as $item) {
    $header = $item['request']['normalized_headers']['Authorization'];
    $this->assertStringContainsString($header[0], $token);
}

$last = end($history);
$url = parse_url($last['request']['url']);
$this->assertFalse(
    "/v3/company/{$this->companyId}/user" == $url['path']
    && $last['request']['method'] == "DELETE"
);


// With Hybrid
$this->hybrid->assertAll(function (Expectation $e) use ($token) {
    return $e->withHeader('Authorization', "Bearer {$token}");
});

$this->hybrid->assertNotLast(function (Expectation $e) {
    return $e->delete("/v3/company/{$this->companyId}/user");
});
```

## Helpful Failure Messages

Whenever an expectation is not met Hybrid shows a helpful, [serialized message](/troubleshooting/#hybrid-s-error-messages) of it's arguments in the console.

## Extendability

Hybrid, and all Chassis based projects, allow you to create your own [filters](/extending/#custom-filters) and [macros](/extending/#custom-macros). Doing so allows you to add your own solutions for complex traversing needs and shorthands.