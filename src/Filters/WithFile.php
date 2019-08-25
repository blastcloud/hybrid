<?php

namespace BlastCloud\Hybrid\Filters;

use BlastCloud\Hybrid\Traits\Forms;
use BlastCloud\Chassis\Helpers\{Disposition, File};
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Chassis\Filters\Base;
use BlastCloud\Chassis\Traits\Helpers;

class WithFile extends Base implements With
{
    use Helpers, Forms;

    private static $CHUNK_SIZE = 16372;

    protected $files = [];
    protected $exclusive = false;

    public function withFile(string $name, File $file)
    {
        $this->files[$name] = $file;
    }

    public function withFiles(array $files, bool $exclusive = false)
    {
        foreach ($files as $key => $file) {
            $this->withFile($key, $file);
        }

        $this->exclusive = $exclusive;
    }

    public function __invoke(array $history): array
    {
        return array_filter($history, function ($item) {
            $body = $item['request']['body'] ?? '';

            $dispositions = [];
            $boundary = $this->getBoundary($item['request']['request_headers'] ?? '');

            foreach ($this->parseMultipartBody($body, $boundary) as $d) {
                if ($d->isFile()) {
                    $dispositions[$d->name] = $d;
                }
            }

            foreach ($this->files as $name => $file) {
                if (!isset($dispositions[$name]) || !$file->compare($dispositions[$name])) {
                    return false;
                }
            }

            return !$this->exclusive || count($dispositions) == count($this->files);
        });
    }

    public function __toString(): string
    {
        $e = $this->exclusive ? 'true' : 'false';
        return "Files: (Exclusive: {$e}) ".json_encode($this->files, JSON_PRETTY_PRINT);
    }
}