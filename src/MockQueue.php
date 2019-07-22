<?php

namespace BlastCloud\Hybrid;

class MockQueue
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
                'error' => []
            ];
        };
    }

    public function append($response)
    {
        $this->responses[] = $response;
    }

    public function count()
    {
        return count($this->responses);
    }

    public function __invoke($method, $url, $options)
    {
        if (empty($this->responses)) {
            throw new \OutOfBoundsException("Mock queue is empty");
        }

        $h = $this->history;
        $h([
                'method' => $method,
                'url' => $url
            ] + $options,
            $options,
            $response = array_shift($this->responses)
        );

        return $response;
    }
}