---
lang: en-US
title: Expectations | Hybrid
---

# Expectations

Expectations are the main way for you to define what you want Hybrid to search for through your HttpClient history. They are used in two separate ways:

- To define the number of times you expect a match to be made before you test your code.
- To assert what Hybrid should search for in your client's history after your code has run.

### expects(InvokedRecorder $times, $message = null)

To mimic the chainable syntax of PHPUnit mock objects, you can create expectations with the `expects()` method and PHPUnit’s own **InvokedRecorders**. These are methods like `$this->once()`, `$this->lessThan($int)`, `$this->never()`, and so on. You may also pass an optional message to be used on failures as the second argument.

```php
public function testExample()
{
    $expectation = $this->hybrid->expects($this->once());
}
```

> All methods on expectations are chainable and can lead directly into the next method. `$expectation->oneMethod()->anotherMethod()->stillAnother();`

## Available Methods

<div class="toc">
    <p>
        <a href="#withendpoint-string-uri-string-verb-verb-string-uri">withEndpoint, verbs</a><br />
        <a href="#withbody-string-body-bool-exclusive-false">withBody</a><br />
        <a href="#withcallback-closure-callback-string-message-null">withCallback</a><br />
        <a href="#withfile-string-fieldname-file-file">withFile</a><br />
        <a href="#withfiles-array-files-bool-exclusive-false">withFiles</a><br />
        <a href="#withform-array-formfields-bool-exclusive-false">withForm</a><br />
    </p>
    <p>
        <a href="#withformfield-string-key-value">withFormField</a><br />
        <a href="#withheader-string-key-string-array-value">withHeader</a><br />
        <a href="#withheaders-array-headers">withHeaders</a><br />
        <a href="#withjson-array-json-bool-exclusive-false">withJson</a><br />
        <a href="#withoption-string-name-string-value">withOption</a><br />
        <a href="#withoptions-array-options">withOptions</a><br />
    </p>
    <p>
        <a href="#withprotocol-protocol">withProtocol</a><br />
        <a href="#withquery-array-query-exclusive-false">withQuery</a><br />
        <a href="#withquerykey-string-key">withQueryKey</a><br />
        <a href="#withquerykeys-array-keys">withQueryKeys</a><br />
        <a href="#withoutquery">withoutQuery<a/><br />
    </p>
</div>

### withEndpoint(string $uri, string $verb), {verb}(string $uri)

To specify the endpoint and method used for an expectation, use the `endpoint()` method or any of the convenience endpoint verb methods.

```php
$this->hybrid->expects($this->once())
    ->withEndpoint("/some-url", "GET");
```

The following convenience verb methods are available to shorten your code. `get`, `post`, `patch`, `put`, `delete`, `options`.

```php
$this->hybrid->expects($this->any())
    ->get("/a-url-for-getting");
```

### withBody(string $body, bool $exclusive = false)

You can expect a certain body on a request by passing a `$body` string to the `withBody()` method.

```php
$this->hybrid->expects($this->once())
    ->withBody("some body string");
```

By default, this method simply checks that the specified body exists somewhere in the request body, but more text may exist. By passing a boolean `true` as the second argument, the method will instead make a strict comparison.

### withCallback(Closure $callback, string $message = null)

You can pass a custom, anonymous method to do ad hoc, on-the-fly, determining if a history item fits your conditions. Your closure should expect a history array, and return `true` or `false` on whether or not the history item passes your test. A history item is an array with the following structure:

```php
// Hybrid history item structure
[
    "request"  => array,
    "response" => Symfony\Component\HttpClient\Response\MockResponse object,
    "options"  => array,
    "error"   => null|string 
]
```

```php
$this->hybrid->expects($this->once())
    ->withCallback(function ($history) {
        return isset($history['request']['request_headers']['some-header']);
    });
```

::: tip
By default, the error message for a callback is simply "Custom callback: \Closure". It's recommended you pass your own message as the second argument to make a more descriptive error about what exactly your closure was testing.
:::

### withFile(string $fieldName, File $file)

You can make a set of expectations about a specific file that is included in a request. To do so, specify the field that the file should be under, and include an instance of the `BlastCloud\Chassis\Helpers\File` class. The `File` class allows you to specify the following attributes of a file that is uploaded via a multipart form:

| Attribute | Description |
|-----------|-------------|
| `contents` | The raw data of a given file. |
| `contentType` | The `mime` type of the file. In HTTP requests, this becomes the `Content-Type` attribute. |
| `filename` | If you choose to name the file something other than its actual file name. |
| `headers` | Multipart forms allow headers on individual [Content-Dispositions](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition). The same checks as `withHeaders` are used here. |

#### Example

There are several ways you can build out the attributes on a `File` object.

```php
use BlastCloud\Hybrid\Helpers\File;

// Specify attributes on instantiation.
$file = new File($contents = null, string $filename = null, string $contentType = null, array $headers = null);

// Pass an associative array to a factory.
$file = File::create([
    'filename' => 'avatar.jpg',
    'contentType' => 'image/jpeg',
    'contents' => fopen('/path/to/file.jpg', 'r')
]);

// Set each attribute individually.
$file = new File();
$file->contents = fopen(__DIR__ . '/path/to/file.jpg', 'r');
$file->filename = 'avatar.jpg';

$this->hybrid->expects($this->once())
    ->withFile('avatar', $file);
```

The `File` class can accept two different ways to specify `contents`:

1. The string contents are given directly.
2. A resource, such as `fopen()`, is given.

::: tip Be Aware
Because the file given resolves down to an in-memory comparison, it's a good idea to only use reasonably small files during testing.
:::

### withFiles(array $files, bool $exclusive = false)

As a shorthand for passing multiple `withFile()` calls, you can pass an associative array of field names with `File` instances as the values.

```php
$this->hybrid->expects($this->once())
    ->withFiles([
        'first' => File::create(['contents' => fopen('/path/to/file.png', 'r')]),
        'second' => File::create(['contents' => 'some text that would be in the second file']),
        // ...
    ]);
```

By default, this method simply checks that the specified files exist somewhere in the request. By passing a boolean `true` as the second argument, the method will instead cause a failure if additional files are found in the request.

### withForm(array $formFields, bool $exclusive = false)

You can expect a specific set of form fields in the body of a post. This method works with both URL encoded and multipart forms.

```php
$this->hybrid->expects($this->once())
    ->withForm([
        'first-name' => 'John',
        'last-name' => 'Snow'
    ]);
```

By default, this method simply checks that all specified fields and values exist in the request body, but more may exist. By passing a boolean `true` as the second argument, the method will instead make a strict comparison and fail if additional fields are found.

### withFormField(string $key, $value)

You can expect a specific form field in the body of a post. This method works with both URL encoded and multipart forms.

```php
$this->hybrid->expects($this->once())
    ->withFormField('first-name', 'John')
    ->withFormField('house-name', 'Snow');
```

### withHeader(string $key, string|array $value)

If you would like to expect a certain header to be on a request, you can provide the header and it’s value.

```php
$this->hybrid->expects($this->once())
    ->withHeader("Authorization", "some-access-token");
```

You can chain together multiple calls to `withHeader()` to individually add different headers. Headers can also be an array of values.

```php
$this->hybrid->expects($this->once())
    ->withHeader("Accept-Encoding", ["gzip", "deflate"])
    ->withHeader("Accept", "application/json");
```

### withHeaders(array $headers)

As a shorthand for multiple `withHeader()` calls, you can pass an array of header keys and values to `withHeaders()`.

```php
$this->hybrid->expects($this->once())
    ->withHeaders([
        "Accept-Encoding" => ["gzip", "deflate"],
        "Accept" => "application/json"
    ]);
```

### withJson(array $json, bool $exclusive = false)

You can expect a certain `JSON` body on a request by passing an array of data to the `withJson()` method.

```php
$this->hybrid->expects($this->once())
    ->withJson(['first' => 'value', 'second' => 'another']);
```
<br />

::: tip Be Aware
This method tests that the passed array exists on the request by first recursively sorting all keys and values in both the request body and the `$json` argument and then making a string comparison.
:::

This means the following scenarios occur:

```php
// Request body
[
    'first' => [
        'a value',
        'another value'
    ],
    'second' => 'second value'
]

// This expectation will pass. Remember, the request body and the
// argument are both recursively sorted before comparison.
$this->hybrid->expects($this->once())
    ->withJson(['another value', 'a value']);

// This expectation will fail
$this->hybrid->expects($this->once())
    ->withJson(['first' => ['another value']]);
    
// This expectation will pass
$this->hybrid->expects($this->once())
    ->withJson(['second' => 'second value']);
```

By default, this method checks only that the passed array of values exists somewhere in the request body. To make an exclusive comparison so that only the data passed exists on the request body, a boolean `true` can be given as the second argument.

### withOption(string $name, string $value)

You can expect a certain HttpClient option by passing a name and value to this method.

```php
$this->hybrid->expects($this->once())
    ->withOption('base_uri', 'http://somewhere.com');
```

### withOptions(array $options)

As a shorthand for multiple `withOption()` calls, you can pass an array of option keys and values to `withOptions()`.

```php
$this->hybrid->expects($this->once())
    ->withOptions([
        'auth_basic' => ['the-username'],
        // ... something else
    ]);
```

### withProtocol($protocol)

You can expect a certain HTTP protocol (1.0, 1.1, 2.0) using the `withProtocol()` method.

```php
$this->hybrid->expects($this->once())
    ->withProtocol(2.0);
```

### withQuery(array $query, $exclusive = false)

You can expect a set of query parameters to appear in the URL of the request by passing an array of key value pairs to match in the URL. The order of query parameters is not considered.

```php
// Example URL: http://example.com/api/v2/customers?from=15&to=25&format=xml

$this->hybrid->expects($this->once())
    ->withQuery([
        'to' => 25,
        'from' => 15
    ]);
```

By default any additional parameters included in the URL are ignored. To enforce only the URL parameters you specify, a boolean `true` can be passed as the second argument.

```php
// Example URL: http://example.com/api/v2/customers?from=15&to=25&format=xml

// With the second argument, the 'format' parameter causes the expectation to fail.
$this->hybrid->expects($this->once())
    ->withQuery([
        'to' => 25,
        'from' => 15
    ], true);
```

### withQueryKey(string $key)

You can specify just the key for a query item, if you either don't care about the value or there is none. For example, ElasticSearch sometimes has a query key but no following value.

```php
// Example URL: http://some-elasticsearch-url?_delete_by_query

$this->guzzler->expects($this->once())
    ->withQueryKey('_delete_by_query');
```

### withQueryKeys(array $keys)

You can specify just the keys you want to appear in the query, but not specifically check any values they may have.

```php
$this->guzzler->expects($this->once())
    ->withQueryKeys(['first', 'second']);
```

### withoutQuery()

If you'd like to ensure no query string is provided in the request at all, this method can be used.

```php
$this->guzzler->expects($this->once())
    ->withoutQuery();
```