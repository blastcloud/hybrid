<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithBody extends Base implements With
{
    protected $body;
    protected $exclusive = false;
    
    public function withBody(string $body, bool $exclusive = false)
    {
        $this->body = $body;
        $this->exclusive = $exclusive;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
        
        });
    }
    
    public function __toString()
    {
        
    }
}