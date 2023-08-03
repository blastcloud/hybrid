---
lang: en-US
title: Mocking Responses | Hybrid
---

# Mocking Responses

There are three main ways to provide responses to return from your client; `queueResponse()` and `queueMany()` methods directly on the `hybrid` instance, and `will()` or its alias `willRespond()` on an expectation.

### queueResponse(...$responses)

The `queueResponse` method is the main way to add responses to your mock handler. All responses should conform to the standard `ResponseInterface`, or a `\Throwable` may also be used. In most cases, it will likely be a `MockResponse` object. That being said, `MockResponse` objects can accept strings, arrays of strings, or `\Iterable` objects as it's body.

```php
public function testSomething()
{
    $this->hybrid->queueResponse(
        new MockResponse("some body", [
            'response_headers' => []
        ])
    );

    // Whatever the first request sent to your client is, 
    // the response above will be returned.
}
```

The method accepts variadic arguments, so you can add as many responses as you like.

```php
// One call with multiple arguments
$iterable = someIterableMethod();

$this->hybrid->queueResponse(
    new MockResponse("some body"),
    new MockResponse($iterable),
    new \Exception('Some Message')
);

// Multiple calls with one response each.
$this->hybrid->queueResponse(new MockResponse("some body"));
$this->hybrid->queueResponse(new MockResponse($iterable));
$this->hybrid->queueResponse(new \Exception('Some Message'));
```

::: tip Be Aware
Whatever order you queue your responses is the order they will be returned from your client, no matter the URI or method of the request. This is a constraint of most mock handlers.

Also, please note that any `\Throwable` object given, such as an exception, will be thrown rather than returned from the client.
:::

### queueMany($response, int $times = 1)

To quickly add multiple responses to the queue without making each one individually, the `queueMany` method can repeat a specific response any number of times you specify.

```php
// Add 5 responses with no body and status code 201 to the queue.
$response = new MockResponse(null, [
    'http_code' => 201
]);

$this->hybrid->queueMany($response, 5);
```

### will($response, int $times = 1), willRespond($response, int $times = 1)

If you are using expectations in your test, you can add responses to the expectation chain with either `will()` or its alias, `willRespond()`. In both cases, you can provide a single response and the number of times it should be added to the queue. This is so that you can make sure to add a response for each expected invocation.

```php
$this->hybrid->expects($this->atLeast(9))
    ->get("/some-uri")
    ->willRespond(new MockResponse(), 12);

$this->hybrid->expects($this->twice())
    ->post("/another-uri")
    ->will(new \Exception("some message"), 2);
```

If you’d like to return different responses from the same expectation, you can still chain your `will()` or `willRespond()` statements.

```php
$this->hybrid->expects($this->exactly(2))
    ->endpoint("/a-url-for-deleting", "DELETE")
    ->will(new MockResponse(null, ['http_code' => 204]))
    ->will(new MockResponse(null, ['http_code' => 210]));
```

::: tip Be Aware
Whatever order you queue your responses is the order they will be returned from your client, no matter the URI or method of the request. This is a constraint of most mock handlers.
:::