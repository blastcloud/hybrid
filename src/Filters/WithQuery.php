<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Chassis\Traits\Helpers;

class WithQuery extends Base implements With
{
    use Helpers;

    protected $query = [];
    protected $keys = [];
    protected $exclusive = false;
    
    public function withQuery(array $query, bool $exclusive = false)
    {
        $this->query = $query;
        $this->exclusive = $exclusive;
    }

    public function withQueryKeys(array $keys)
    {
        $this->keys = $keys;
    }

    public function withQueryKey(string $key)
    {
        $this->keys[] = $key;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
            if (array_diff($this->keys, array_keys($call['request']['query']))) {
                return false;
            }

            return $this->verifyFields($this->query, $call['request']['query'], $this->exclusive);
        });
    }
    
    public function __toString(): string
    {
        $e = $this->exclusive ? 'true' : 'false';
        return "Query: (Exclusive: {$e})".json_encode($this->query, JSON_PRETTY_PRINT);
    }
}
