<?php

namespace BlastCloud\Hybrid;

use BlastCloud\Chassis\Chassis;
use PHPUnit\Framework\MockObject\Matcher\InvokedRecorder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

class Hybrid extends Chassis
{
    /** @var MockQueue */
    protected $mockHandler;

    protected $history = [];

    protected $expectationClass = Expectation::class;

    public function __construct(TestCase $testInstance)
    {
        parent::__construct($testInstance);

        Expectation::addNamespace(__NAMESPACE__.'\\Filters');
    }

    public function getClient(array $options = [])
    {
        $this->mockHandler = new MockQueue($this->history, $options);

        return new MockHttpClient($this->mockHandler, $options['base_uri'] ?? null);
    }

    /**
     * @param InvokedRecorder $argument
     * @return Expectation
     */
    public function expects(InvokedRecorder $argument)
    {
        return parent::expects($argument);
    }
}