<?php

namespace BlastCloud\Hybrid;

trait UsesHybrid
{
    /** @var Hybrid */
    public $hybrid;

    /**
     * @before
     */
    public function setUpHyrbid()
    {
        $engine = $this->engineName();

        $this->$engine = new Hybrid($this);
    }

    private function engineName()
    {
        return defined('self::ENGINE_NAME')
            ? self::ENGINE_NAME
            : 'hybrid';
    }

    /**
     * @after
     * Run through the list of expectations that were made and
     * evaluate all requests in the history. Closure::call()
     * is used to hide this method from the user APIs.
     */
    public function runHybridAssertions()
    {
        $name = $this->engineName();
        (function () {
            $this->runExpectations();
        })->call($this->$name);
    }
}