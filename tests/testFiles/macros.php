<?php

use BlastCloud\Hybrid\Expectation;

Expectation::macro('fromFile', function (Expectation $e, $url) {
    return $e->post($url);
});