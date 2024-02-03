<?php

namespace BlastCloud\Hybrid;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

trait UsesHybrid
{
    /** @var Hybrid */
    public $hybrid;

    #[Before]
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
     */
    #[After]
    public function runHybridExpectations()
    {
        $name = $this->engineName();
        (function () {
            $this->runExpectations();
        })->call($this->$name);
    }
}