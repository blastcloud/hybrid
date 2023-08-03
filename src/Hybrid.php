<?php

namespace BlastCloud\Hybrid;

use BlastCloud\Chassis\Chassis;
use PHPUnit\Framework\MockObject\Matcher\InvokedRecorder;
use PHPUnit\Framework\TestCase;

class Hybrid extends Chassis
{
    /** @var MockQueue */
    protected $mockHandler;

    protected $history = [];

    public function __construct(TestCase $testInstance)
    {
        parent::__construct($testInstance);

        Expectation::addNamespace(__NAMESPACE__.'\\Filters');
    }

    /**
     * Return a new mocked HttpClient object with the provided $options.
     *
     * @param array $options
     * @return MockHttpClient|mixed
     */
    public function getClient(array $options = [])
    {
        $this->mockHandler = new MockQueue($this->history, $options);

        return new MockHttpClient($this->mockHandler, $options['base_uri'] ?? null);
    }

    protected function createExpectation($argument = null): Expectation
    {
        return new Expectation($argument, $this);
    }

    /**
     * @param mixed $argument
     * @return Expectation
     */
    public function expects($argument)
    {
        return parent::expects($argument);
    }
}