<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\Mask;

class MaskTest extends TestCase
{
    public function testConstruct()
    {
        $obj = new Mask();
        $this->assertInstanceOf('Tarsius\Mask', $obj);
    }
}
