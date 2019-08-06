<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithEndpoint extends Base implements With
{
    public $endpoint;
    protected $method;

    public function withEndpoint($endpoint, string $method)
    {
        $this->endpoint = $endpoint;
        $this->method = $method;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
        
        });
    }
    
    public function __toString()
    {
        return str_pad("Method:", self::STR_PAD) . $this->method;
    }
}