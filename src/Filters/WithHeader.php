<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithHeader extends Base implements With
{
    protected $headers = [];
    
    public function withHeader(string $key, $value)
    {
        $this->headers[$key] = $value;
    }
    
    public function withHeaders(array $values)
    {
        foreach($values as $key => $value) {
            $this->withHeader($key, $value);
        }
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
            foreach ($this->headers as $key => $value) {
                $header = $call['request']['headers'][strtolower($key)] ?? [];

                if ($header != $value && !in_array($value, $header)) {
                    return false;
                }
            }

            return true;
        });
    }
    
    public function __toString(): string
    {
        return str_pad('Headers:', self::STR_PAD)
            . json_encode($this->headers, JSON_PRETTY_PRINT);
    }
}