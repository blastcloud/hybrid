<?php

use BlastCloud\Hybrid\Expectation;

foreach (BlastCloud\Hybrid\Filters\WithEndpoint::VERBS as $verb) {
    Expectation::macro(
        strtolower($verb), 
        function (BlastCloud\Chassis\Expectation $expectation, $url) use ($verb) {
            return $expectation->withEndpoint($url, $verb);
        }
    );
}

Expectation::macro('withoutQuery', function (BlastCloud\Chassis\Expectation $e) {
    return $e->withQuery([], true);
});