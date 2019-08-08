<?php

foreach (BlastCloud\Hybrid\Filters\WithEndpoint::VERBS as $verb) {
    BlastCloud\Chassis\Expectation::macro(
        strtolower($verb), 
        function ($expectation, $url) use ($verb) {
            return $expectation->withEndpoint($url, $verb);
        }
    );
}