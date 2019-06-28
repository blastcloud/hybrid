<?php

namespace BlastCloud\Hybrid;

class MockQueue
{
    /** @var \Closure */
    public $history;

    public $responses = [];

    public function __construct(array &$history)
    {
        $this->history = function ($request, $response) use (&$history) {
            $history[] = [
                'request' => $request,
                'response' => $response
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
            $response = array_shift($this->responses)
        );

        return $response;
    }
}