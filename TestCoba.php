<?php

use PHPUnit\Framework\TestCase;
use Tests\Cobaya;

class TestCoba extends TestCase
{
    function testCobaku()
    {
        $this->assertSame("helo", (new Cobaya())->cobaYa() );
    }
}