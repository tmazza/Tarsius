<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

use PHPUnit\Framework\TestCase;
use Tarsius\Tarsius;
use Tarsius\MaskGenerator;

class MaskGeneratorTest extends TestCase
{
    public function testConstruct()
    {
        $imageName = __DIR__  . '/images/i1.jpg';
        $config = $this->getConfig();

        $obj = new MaskGenerator('teste1', $imageName,$config);

        $this->assertInstanceOf('Tarsius\MaskGenerator', $obj);
    }

    public function testGenerate()
    {
        $imageName = __DIR__  . '/images/i1.jpg';
        $config = $this->getConfig();

        $obj = new MaskGenerator('teste2', $imageName,$config);
        // $obj->generate();
        exit;
        // $this->assertInstanceOf('Tarsius\MaskGenerator', $obj);
    }

    private function getConfig()
    {
        return [
          'nome' => 'tj100',
          'regioes' => [
            [
            'tipo' => 0,
             'p1' => [43.883331298828,1498],
             'p2' => [2233.8833312988,2681],
             'colunasPorLinha' => 20,
             'agrupaObjetos' => 5,
             'minArea' => 300,
             'maxArea' => 3000,
             'id' => function($b,$l,$o) {
                    $idQuestao = str_pad($b*25 + $l+1,3,'0',STR_PAD_LEFT);
                    return 'e-'.$idQuestao.'-'.($o+1);
                },
             'casoTrue' => function($b,$l,$o) { 
                  switch ($o){
                    case 0: return 'A';
                    case 1: return 'B';
                    case 2: return 'C';
                    case 3: return 'D';
                    case 4: return 'E';
                  }
                },
              'casoFalse' => 'W',
            ],
            [
              'tipo' => 0,
              'p1' => [2053.8833312988,3012.75],
              'p2' => [2127.8833312988,3059.75],
              'colunasPorLinha' => 15,
              'agrupaObjetos' => 5,
              'minArea' => 300,
              'maxArea' => 3000,
              'id' => 'ausente',
              'casoTrue' => 'S',
              'casoFalse' => 'N',
            ],
            [
              'tipo' => 1,
              'p1' => [53.883331298828,3189.75],
              'p2' => [359.88333129883,3268.75],
              'colunasPorLinha' => 15,
              'agrupaObjetos' => 5,
              'minArea' => 300,
              'maxArea' => 3000,
              'id' => 'template',
              'casoTrue' => function($b,$l,$o) { 
                  switch ($o){
                    case 0: return 'A';
                    case 1: return 'B';
                    case 2: return 'C';
                    case 3: return 'D';
                    case 4: return 'E';
                  }
                },
              'casoFalse' => 'W',
            ],
          ],
          'formatoSaida' => [
                'respostas' => [
                    'match' => '/^e-.*-\d$/',
                    'order' => false,
                ],
            ],
        ];
    }



}