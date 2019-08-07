<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithVersion extends Base implements With
{
    protected $version;
    
    public function withVersion($version)
    {
        $this->version = $version;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
            return $call['request']['http_version'] == $this->version;
        });
    }
    
    public function __toString(): string
    {
        return str_pad("Version:", self::STR_PAD).$this->version;
    }
}