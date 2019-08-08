<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithJson extends Base implements With
{
    protected $json = [];
    protected $exclusive = false;
    
    public function withJson(array $json, bool $exclusive = false)
    {
    
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
        
        });
    }
    
    public function __toString(): string
    {
        return str_pad("JSON:", self::STR_PAD)
            .json_encode($this->json, JSON_PRETTY_PRINT);
    }
}