<?php

use BlastCloud\Hybrid\Expectation;

Expectation::macro('fromFile', function (BlastCloud\Chassis\Expectation $e, $url) {
    return $e->post($url);
});