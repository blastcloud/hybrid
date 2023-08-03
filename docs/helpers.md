---
lang: en-US
title: Helpers | Hybrid
---

# Helpers

The following helper methods can be used in addition to expectations and assertions for any custom logic or checks that need to be made.

### getHistory(?int $index, $subIndex = null)

To retrieve the client’s raw history, this method can be used.

```php
$history = $this->hybrid->getHistory();
// Returns the entire history array
```

The shape of the history array Hybrid creates is is as follows:

```php
$history = [
    // Hybrid history item structure
    [
        "request"  => array,
        "response" => Symfony\Component\HttpClient\Response\MockResponse object,
        "options"  => array,
        "error"   => null|string 
    ]
    // ...
];
```

Individual indexes and sub-indexes of the history can also be requested directly.

```php
$second = $this->hybrid->getHistory(1);
/**
* [
*   'request'  => array
*   'response' => object
*   'options'  => array
*   'errors'   => null|string
* ]
*/

$options = $this->hybrid->getHistory(4, 'options');
/**
* [
*   'base_uri' => 'http://somewhere.com',
*   // ...
* ]
*/
```

### historyCount()

Retrieve the total number of requests that were made on the client.

```php
$this->client->request('GET', '/first');
$this->client->request('DELETE', '/second');

echo $this->hybrid->historyCount();
// 2
```

### queueCount()

Retrieve the total number of response items in the mock handler's queue.

````php
echo $this->hybrid->queueCount();
// 0

$this->hybrid->queueMany(new MockResponse(), 6);

echo $this->hybrid->queueCount();
// 6
```