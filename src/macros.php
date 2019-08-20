<?php

use BlastCloud\Hybrid\Expectation;

foreach (Expectation::VERBS as $verb) {
    Expectation::macro(strtolower($verb), function (Expectation $e, $url) use ($verb) {
        return $e->withEndpoint($url, strtoupper($verb));
    });
}