<?php

namespace BlastCloud\Hybrid;

trait UsesHybrid
{
    public Hybrid $hybrid;

    /**
     * @before
     * @return void
     */
    public function setUpHybrid()
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
     * Run through the list of expectations that were made and
     * evaluate all requests in the history. Closure::call()
     * is used to hide this method from the user APIs.
     *
     * @after
     */
    public function runHybridExpectations()
    {
        $name = $this->engineName();
        (function () {
            $this->runExpectations();
        })->call($this->$name);
    }
}