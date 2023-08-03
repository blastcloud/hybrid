<?php

use BlastCloud\Hybrid\Expectation;

foreach (BlastCloud\Hybrid\Filters\WithEndpoint::VERBS as $verb) {
    Expectation::macro(
        strtolower($verb), 
        function (Expectation $expectation, $url) use ($verb) {
            return $expectation->withEndpoint($url, $verb);
        }
    );
}

Expectation::macro('withoutQuery', function (Expectation $e) {
    return $e->withQuery([], true);
});