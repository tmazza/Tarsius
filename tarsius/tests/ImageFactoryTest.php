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
        # tipo gd
        $obj = ImageFactory::create(ImageFactory::GD);
        $this->assertInstanceOf('Tarsius\ImageGd', $obj);
    }
}
