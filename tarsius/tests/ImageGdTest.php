<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\ImageGd;

class ImageGdTest extends TestCase
{
    private $imageName;
    private $imageName2;

    public function __construct()
    {
        $this->imageName = __DIR__  . '/images/i1.jpg';
        $this->imageName2 = __DIR__  . '/images/i2.jpg';
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
        $avaliada = $this->invokeMethod($obj, 'getResolution');
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

    public function testGetPointsBetween()
    {
        $obj = new ImageGd($this->imageName2);
        $obj->load();

        $p1 = [10,10]; $p2 = [20,20]; # quadrado do centro

        $pontos = $obj->getPointsBetween($p1, $p2);

        $this->assertEquals(count($pontos), 10);
        foreach ($pontos as $p) {
            $this->assertEquals(count($p), 10);
        }
        
    }

    public function testGetObjectsBetween()
    {
        $obj = new ImageGd($this->imageName2);
        $obj->load();

        $p1 = [0,0]; $p2 = [30,30]; # toda a imagem

        $objetos = $obj->getObjectsBetween($p1, $p2, 10, 100);

        $this->assertEquals(count($objetos), 11);
        
    }


}
