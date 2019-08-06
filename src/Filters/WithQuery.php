<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Base;
use BlastCloud\Chassis\Interface\With;

class WithQuery extends Base implements With
{
    protected $query = [];
    protected $exclusive = false;
    
    public function withQuery(array $query, bool $exclusive = false)
    {
        $this->query = $query;
        $this->exclusive = $exclusive;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
        
        });
    }
    
    public function __toString()
    {
        $e = $this->exclusive ? 'true' : 'false';
        return "Query: (Exclusive: {$e})".json_encode($this->query, JSON_PRETTY_PRINT);
    }
}
