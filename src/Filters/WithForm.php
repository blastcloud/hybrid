<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;

class WithForm extends Base implements With
{
    protected $fields = [];
    protected $exclusive = false;
    
    public function withFormField(string $key, $value)
    {
        $this->fields[$key] = $value;
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
    
    }
    
    public function __toString()
    {
        return str_pad("Form:", self::STR_PAD)
            .json_encode($this->fields, JSON_PRETTY_PRINT);
    }
}