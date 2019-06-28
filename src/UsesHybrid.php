<?php

namespace BlastCloud\Hybrid;

trait UsesHybrid
{
    public $hybrid;

    /**
     * @before
     */
    public function setUpHyrbid()
    {

    }

    /**
     * @after
     * Run through the list of expectations that were made and
     * evaluate all requests in the history. Closure::call()
     * is used to hide this method from the user APIs.
     */
    public function runHybridAssertions()
    {
        (function () {
            $this->runExpectations();
        })->call($this->hybrid);
    }
}