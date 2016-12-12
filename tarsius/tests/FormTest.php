<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\Tarsius;
use Tarsius\Form;

class FormTest extends TestCase
{
    public function testConstruct()
    {
        /**
         * @todo criar imagem e template para teste
         */
        $imageName = __DIR__  . '/images/i1.jpg';
        $maskName = __DIR__ . '/templates/template.json';

        $obj = new Form($imageName,$maskName);

        $this->assertInstanceOf('Tarsius\Form', $obj);
    }
    
    public function testEvaluate()
    {
        $imageName = __DIR__  . '/images/i2.jpg';
        $maskName = __DIR__ . '/templates/template.json';

        Tarsius::config([
            'minArea' => 200,
        ]);

        $obj = new Form($imageName,$maskName);
        $results = $obj->evaluate();

        # todo: ...
        // $this->assertInstanceOf('Tarsius\Form', $obj);
    }
    
    public function testEvaluate2()
    {
        $imageName = __DIR__  . '/images/i3.jpg';
        $maskName = __DIR__ . '/templates/template.json';

        Tarsius::config([
            'minArea' => 200,
            'threshold' => 180,
        ]);

        $obj = new Form($imageName,$maskName);
        $results = $obj->evaluate();

        # todo: ...
        // $this->assertInstanceOf('Tarsius\Form', $obj);
    }
    
    public function testEvaluate3()
    {
        $imageName = __DIR__  . '/images/i4.jpg';
        $maskName = __DIR__ . '/templates/template.json';

        Tarsius::config([
            'minArea' => 200,
        ]);

        $obj = new Form($imageName,$maskName);
        $results = $obj->evaluate();

        # todo: ...
        // $this->assertInstanceOf('Tarsius\Form', $obj);
    }
}