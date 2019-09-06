<?php

use BlastCloud\Hybrid\Expectation;

foreach (BlastCloud\Hybrid\Filters\WithEndpoint::VERBS as $verb) {
    BlastCloud\Chassis\Expectation::macro(
        strtolower($verb), 
        function (Expectation $expectation, $url) use ($verb) {
            return $expectation->withEndpoint($url, $verb);
        }
    );
}