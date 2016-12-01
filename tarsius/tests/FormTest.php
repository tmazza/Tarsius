<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\Form;

class FormTest extends TestCase
{
    public function testConstruct()
    {
        /**
         * @todo criar imagem e template para teste
         */
        $imageName = __DIR__  . '/images/formTest1.jpg';
        $maskName = __DIR__ . '/templates/formTest1.json';

        $obj = new Form($imageName,$maskName);

        $this->assertInstanceOf('Tarsius\Form', $obj);
    }
    
    public function testEvaluate()
    {
        /**
         * @todo criar imagem e template para teste
         */
        $imageName = __DIR__  . '/images/formTest1.jpg';
        $maskName = __DIR__ . '/templates/formTest1.json';

        $obj = new Form($imageName,$maskName);
        $obj->evaluate();
        # todo: ...
        // $this->assertInstanceOf('Tarsius\Form', $obj);
    }
}