<?php

namespace Tests\TestFiles;

use BlastCloud\Hybrid\Filters\WithBody as Base;
use BlastCloud\Chassis\Interfaces\With;
use BlastCloud\Hybrid\UsesHybrid;

class WithBody extends Base implements With
{
    use UsesHybrid;

    public static $bodyString;

    public function withBody($body, bool $exclusive = false)
    {
        self::$bodyString = $body;
        parent::withBody($body, $exclusive);
    }
}