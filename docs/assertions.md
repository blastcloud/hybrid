---
lang: en-US
title: Assertions | Hybrid
---

# Assertions

While [Expectations](/expectations/) work great for cases where you don’t care about the order of requests to your client, you may find times where you want to verify either the order of requests in your client’s history, or you may want to make assertions about the entirety of its history. Hybrid provides several convenience assertions for exactly this scenario.

Assertions are also intended to be made after the call to your code under test while Expectations are laid out before.

## Available Methods

<div class="toc">
    <p>
        <a href="#assertnohistory-message-null">assertNoHistory</a><br />
        <a href="#asserthistorycount-int-count-message-null">assertHistoryCount</a><br />
        <a href="#assertfirst-closure-expect-message-null">assertFirst</a><br />
        <a href="#assertnotfirst-closure-expect-message-null">assertNotFirst</a><br />
    </p>
    <p>
        <a href="#assertlast-closure-expect-message-null">assertLast</a><br />
        <a href="#assertnotlast-closure-expect-message-null">assertNotLast</a><br />
        <a href="#assertindexes-array-indexes-closure-expect-message-null">assertIndexes</a><br />
    </p>
    <p>
        <a href="#assertnotindexes-array-indexes-closure-expect-message-null">assertNotIndexes</a><br />
        <a href="#assertall-closure-expect-message-null">assertAll</a><br />
        <a href="#assertnone-closure-expect-message-null">assertNone</a><br />
    </p>
</div>

### assertNoHistory($message = null)

To assert that your code did not make any requests at all, you can use the `assertNoHistory()` method, and pass an optional message argument.

```php
public function testSomething()
{
    // ...

    $this->hybrid->assertNoHistory();
}
```

### assertHistoryCount(int $count, $message = null)

This method can assert that the client received exactly the specified number of requests, regardless of what the requests were.

```php
public function testSomething()
{
    // ...

    $this->hybrid->assertHistoryCount(4);
}
```

### assertFirst(Closure $expect, $message = null)

Assertions can be made specifically against the first item in the client history. The first argument should be a closure that receives an `Expectation` and an optional error message can be passed as the second argument.

```php
use BlastCloud\Hybrid\Expectation;

// ...

$this->hybrid->assertFirst(function (Expectation $e) {
    return $e->post("/a-url")
        ->withProtocol(1.1)
        ->withHeader("XSRF", "some-string");
});
```

### assertNotFirst(Closure $expect, $message = null)

Assert that the first request in history does not meet the given expectation. The first argument should be a closure that receives an `Expectation` and an optional error message can be passed as the second argument.

```php
$this->hybrid->assertNotFirst(function ($expect) {
    return $expect->options('/some-url');
});
```

### assertLast(Closure $expect, $message = null)

Assertions can be made specifically against the last item in the client history. The first argument should be a closure that receives an `Expectation` and an optional error message can be passed as the second argument.

```php
$this->hybrid->assertLast(function ($expect) {
    return $expect->get("/some-getter");
});
```

### assertNotLast(Closure $expect, $message = null)

Assert that the last request in history does not meet the given expectation. The first argument should be a closure that receives an `Expectation` and an optional error message can be passed as the second argument.

```php
$this->hybrid->assertNotLast(function ($expect) {
    return $expect->post('/some-url');
});
```

### assertIndexes(array $indexes, Closure $expect, $message = null)

Assertions can be made against any specific index or set of indexes in the client history. The first argument should be an array of integers that correspond to the indexes of history items. The second argument should be a closure that receives an `Expectation` and an optional error message can be passed as the third argument.

```php
$this->hybrid->assertIndexes([2, 3, 6], function ($expect) {
    return $expect->withBody("some body string");
});
```

### assertNotIndexes(array $indexes, Closure $expect, $message = null)

Assertions can be made in the negative against any specific index or set of indexes in the client history. The first argument should be an array of integers that correspond to the indexes of history items. The second argument should be a closure that receives an `Expectation` and an optional error message can be passed as the third argument.

```php
$this->hybrid->assertNotIndexes([2, 3, 6], function ($expect) {
    return $expect->delete('/some-url')
        ->withJson(['id-to-delete' => 42]);
});
```

### assertAll(Closure $expect, $message = null)

This method can be used to assert that every request in the client’s history meets the expectation. For example, you may want to ensure that every request uses a certain authentication header or API token. The first argument should be a closure that receives an `Expectation` and an optional error message as the second argument.

```php
$this->hybrid->assertAll(function ($expect) use ($token) {
    return $expect->withHeader("Authorization", $token);

    // Or maybe

    return $expect->withQuery(['api-key' => $token]);
});
```

### assertNone(Closure $expect, $message = null)

This method can be used to assert that no request, given that any have been made, meet the expectation.

```php
$this->hybrid->assertNone(function ($expect) {
    return $expect->delete("/some-dangerous-thing-to-delete");
});
```

> You may notice that `assertNone()` has the same effect as `expects($this->never())`. The only real difference is preference.