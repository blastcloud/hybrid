<?php

namespace BlastCloud\Hybrid;

use BlastCloud\Chassis\Interfaces\MockHandler;

class MockQueue implements MockHandler
{
    /** @var \Closure */
    public $history;

    public $responses = [];

    public $options = [];

    public function __construct(array &$history, array $options = [])
    {
        $this->options = $options;

        $this->history = function ($request, $ops, $response) use (&$history, $options) {
            $history[] = [
                'request' => $request,
                'response' => $response,
                'options' => array_merge($this->options, $ops),
                'error' => ($response instanceof \Throwable)
                    ? $response->getMessage()
                    : []
            ];
        };
    }

    public function append($response): void
    {
        $this->responses[] = $response;
    }

    public function count(): int
    {
        return count($this->responses);
    }

    public function __invoke($method, $url, $options)
    {
        if (empty($this->responses)) {
            throw new \OutOfBoundsException("Mock queue is empty");
        }

        $h = $this->history;

        // Get rid of the body. It doesn't make sense to put in the options later on.
        $o = $options;
        unset($o['body']);

        $h([
                'method' => $method,
                'url' => $url
            ] + $options,
            $o,
            $response = array_shift($this->responses)
        );

        if ($response instanceof \Throwable)
        {
            throw $response;
        }

        return $response;
    }
}