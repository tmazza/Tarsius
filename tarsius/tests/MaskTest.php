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
        $maskName = __DIR__ . '/templates/formTest1.json';
        $obj = new Mask($maskName);
        $this->assertInstanceOf('Tarsius\Mask', $obj);
    }
}
