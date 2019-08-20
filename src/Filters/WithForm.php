<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Chassis\Traits\Helpers;
use GuzzleHttp\Psr7\MultipartStream;

class WithForm extends Base implements With
{
    use Helpers;

    protected $form = [];
    protected $exclusive = false;
    
    public function withFormField(string $key, $value)
    {
        $this->form[$key] = $value;
    }
    
    public function withForm(array $fields, bool $exclusive = false)
    {
        foreach ($fields as $key => $value) {
            $this->withFormField($key, $value);
        }
        
        $this->exclusive = $exclusive;
    }
    
    public function __invoke(array $history): array
    {
        return array_filter($history, function ($call) {
            $body = $call['request']->getBody();

            if ($body instanceof MultipartStream) {
                $parsed = [];
                foreach ($this->parseMultipartBody($body) as $d) {
                    if (!$d->isFile()) $parsed[$d->name] = $d->contents;
                }
            } else {
                parse_str($body, $parsed);
            }

            return $this->verifyFields($this->form, $parsed, $this->exclusive);
        });
    }
    
    public function __toString(): string
    {
        $e = $this->exclusive ? 'true' : 'false';
        return "Form: (Exclusive: {$e}) "
            .json_encode($this->form, JSON_PRETTY_PRINT);
    }
}