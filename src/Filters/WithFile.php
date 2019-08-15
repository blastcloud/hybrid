<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Chassis\Helpers\File;

class WithFile extends Base implements With
{
    protected $files = [];
    
    public function withFile(string $field, File $file)
    {
        $this->files[$field] = $file;
    }
    
    public function withFiles(array $fields)
    {
        foreach ($fields as $field => $value) {
            $this->withFile($field, $value);
        }
    }
    
    public function __invoke(array $history)
    {
    
    }
    
    public function __toString(): string
    {
        return str_pad('Files:', self::STR_PAD)
          .json_encode($this->files);
    }
}