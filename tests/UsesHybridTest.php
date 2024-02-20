<?php

namespace Tests;

use BlastCloud\Hybrid\Hybrid;
use BlastCloud\Hybrid\UsesHybrid;
use PHPUnit\Framework\TestCase;

class UsesHybridTest extends TestCase
{
    use UsesHybrid;

    public function testMakeHybrid()
    {
        $this->assertInstanceOf(Hybrid::class, $this->hybrid);
    }

}