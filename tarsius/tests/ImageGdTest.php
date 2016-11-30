<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\ImageGd;

class ImageGdTest extends TestCase
{
    private $imageName;

    public function __construct()
    {
        $this->imageName = __DIR__  . '/images/formTest1.jpg';
    }

    public function testConstruct()
    {
        $obj = new ImageGd($this->imageName);
        $this->assertInstanceOf('Tarsius\ImageGd', $obj);
    }

    public function testLoad()
    {
        $this->assertEquals(true, true);
    }

    public function testGetResolucao()
    {
        $obj = new ImageGd($this->imageName);
        $esperado = 300;
        $avaliada = $this->invokeMethod($obj, 'getResolucao');
        $this->assertEquals($esperado, $avaliada);
    }


    /**
     * Para possibilitar chamada do método privado
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }


}
