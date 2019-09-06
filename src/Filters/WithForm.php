<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Chassis\Traits\Helpers;
use BlastCloud\Hybrid\Traits\Forms;

class WithForm extends Base implements With
{
    use Helpers, Forms;

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
            $body = $call['request']['body'] ?? '';

            // TODO: Once HttpClient is stable, remove the excess here and just use the
            // proper index name and shape; whatever that is.
            $boundary = $this->getBoundary(
                $call['request']['normalized_headers']
                ?? $call['request']['request_headers']
                ?? []
            );

            if (!empty($boundary)) {
                $parsed = [];
                foreach ($this->parseMultipartBody($body, $boundary) as $d) {
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