<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithProtocol extends Base implements With
{
    protected $version;
    
    public function withProtocol($version)
    {
        $this->protocol = $version;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
        
        });
    }
    
    public function __toString()
    {
        return str_pad("Protocol:", self::STR_PAD).$this->version;
    }
}