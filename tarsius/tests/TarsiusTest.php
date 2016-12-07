<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\Tarsius;

class TarsiusTest extends TestCase
{
    public function testConfig()
    {
        Tarsius::config([
            'threshold' => 140,
        ]);

        $this->assertEquals(140, Tarsius::$threshold);
    }
}