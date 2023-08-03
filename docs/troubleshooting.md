---
lang: en-US
title: Troubleshooting | Hybrid
---

# Troubleshooting

This page lists common issues or message you might run into while building out your tests with Hybrid.

## Mock queue is empty

```bash
OutOfBoundsException: Mock queue is empty
```

This exception is thrown by Hybrid's mock handler when you have either not provided a response to return from the mock queue, or have run out of responses. When Hybrid returns a response from the mock handler, it removes that response from the queue entirely.

```php{4}
// In your tests
$this->hybrid->expects($this->anything())
    ->get('/some-url')
    ->willRespond(new MockResponse('Success', [
        'http_code' => 201
    ]));

// Code under test
$this->instance->doSomething();
```

In the example above, we added only 1 response to the queue before executing our code under test. Then in that code under test we might end up making two requests.

```php 
// In your production code
$response = $this->client->request('GET', '/some-url');

// Later in your production code
$response2 = $this->client->request('GET', '/some-url');
```

## Hybrid's Error Messages

In order to be helpful, Hybrid `Expectations` are serialized into a user-friendly list of filters that exist on a failed expectation. For example, the following `Expectation`

```php
$this->hybrid->expects($this->once())
    ->withHeader('Auth', 'Some-key')
    ->withQuery(['first' => 'value', 'second' => 'another'])
    ->get('/a-url-for/querying')
    ->will(new MockResponse());
```

Would be serialized to a string error in the console as

```bash
Method was expected to be called 1 times, actually called 0 times. 

Expectation: /a-url-for/querying
-----------------------------
Headers:  {
    "Auth": "Some-key"
}
Query: (Exclusive: false){
    "first": "value",
    "second": "another"
}
Method:   GET
```