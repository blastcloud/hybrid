<?php

namespace BlastCloud\Hybrid\Traits;

use BlastCloud\Chassis\Traits\Helpers;

trait Forms
{
    use Helpers;

    protected function getBoundary(array $headers): string
    {
        foreach ($headers as $header) {
            if (is_array($header)) {
                $header = $header[0];
            }

            if ($boundary = $this->parseHeaderVariables('boundary', $header)) {
                return $boundary;
            }
        }

        return '';
    }
}