<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\Object;

class ObjectTest extends TestCase
{
    public function testGetPoints()
    {
        $obj = new Object();
        $obj->addPoint(1,2);
        $obj->addPoint(2,2);
        $obj->addPoint(1,3);

        $points = $obj->getPoints();

        $this->assertEquals(3, count($points));
    }

    public function testGetArea()
    {
        $obj = new Object();
        $obj->addPoint(1,2);
        $obj->addPoint(2,2);
        $obj->addPoint(1,3);
        $obj->addPoint(4,3);

        $this->assertEquals(4, $obj->getArea());
    }

    public function testGetCenter()
    {
        $obj = new Object();
        $obj->addPoint(2,2);
        $obj->addPoint(1,2);
        $obj->addPoint(2,1);
        $obj->addPoint(3,2);
        $obj->addPoint(2,3);

        $this->assertEquals([2,2], $obj->getCenter());
    }
}
