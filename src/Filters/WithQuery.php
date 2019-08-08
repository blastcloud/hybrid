<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Chassis\Traits\Helpers;

class WithQuery extends Base implements With
{
    use Helpers;

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
            return $this->verifyFields($this->query, $call['request']['query'], $this->exclusive);
        });
    }
    
    public function __toString(): string
    {
        $e = $this->exclusive ? 'true' : 'false';
        return "Query: (Exclusive: {$e})".json_encode($this->query, JSON_PRETTY_PRINT);
    }
}
