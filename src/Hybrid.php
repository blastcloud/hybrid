<?php

namespace BlastCloud\Hybrid;

use BlastCloud\Chassis\Chassis;
use PHPUnit\Framework\TestCase;

class Hybrid extends Chassis
{
    /** @var MockQueue */
    protected $mockHandler;

    protected array $history = [];

    public function __construct(TestCase $testInstance)
    {
        parent::__construct($testInstance);

        Expectation::addNamespace(__NAMESPACE__.'\\Filters');
    }

    /**
     * Return a new mocked HttpClient object with the provided $options.
     */
    public function getClient(array $options = []): mixed
    {
        $this->mockHandler = new MockQueue($this->history, $options);

        return new MockHttpClient($this->mockHandler, $options['base_uri'] ?? null);
    }

    protected function createExpectation($argument = null): Expectation
    {
        return new Expectation($argument, $this);
    }

    public function expects(mixed $argument): \BlastCloud\Chassis\Expectation
    {
        return parent::expects($argument);
    }
}