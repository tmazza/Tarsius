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
        $maskName = __DIR__ . '/templates/template.json';
        $obj = new Mask($maskName);
        $this->assertInstanceOf('Tarsius\Mask', $obj);
    }
}
