<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\ImageFactory;

class ImageFactoryTest extends TestCase
{
    public function testConstruct()
    {
        $obj = new ImageFactory();
        $this->assertInstanceOf('Tarsius\ImageFactory', $obj);
    }

    public function testCreate()
    {
        $imageName = __DIR__  . '/images/i1.jpg';
        # tipo gd
        $obj = ImageFactory::create($imageName, ImageFactory::GD);
        $this->assertInstanceOf('Tarsius\ImageGd', $obj);
    }
}
