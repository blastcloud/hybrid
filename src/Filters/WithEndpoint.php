<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;
use GuzzleHttp\Psr7\Uri;

class WithEndpoint extends Base implements With
{
    public $endpoint;
    protected $method;
    
    const VERBS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS'
    ];

    public function withEndpoint(string $uri, string $method)
    {
        $this->endpoint = parse_url($uri)['path'];
        $this->method = $method;
    }

    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
            $url = parse_url($call['request']['url']);
            return $call['request']['method'] == $this->method
                && $url['path'] == $this->endpoint;
        });
    }

    public function __toString(): string
    {
        return str_pad('Method:', self::STR_PAD).$this->method;
    }
}