<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithProtocol extends Base implements With
{
    protected $version;
    
    public function withProtocol($protocol)
    {
        $this->version = $protocol;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
            return $call['request']['http_version'] == $this->version;
        });
    }
    
    public function __toString(): string
    {
        return str_pad("Protocol:", self::STR_PAD).$this->version;
    }
}