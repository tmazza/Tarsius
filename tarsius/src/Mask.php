<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

/**
 * @todo Definir do utilizados no templeta
 */
class Mask
{
    # Numeração das âncoras
    const ANCHOR_TOP_LEFT = 1;
    const ANCHOR_TOP_RIGHT = 2;
    const ANCHOR_BOTTOM_RIGHT = 3;
    const ANCHOR_BOTTOM_LEFT = 4;
    
    # Nome dos parâmetros no template
    const FORMAT_OUTPUT = 'formatoSaida';
    const REGIONS = 'regioes';
    const START_POINT = 'ancora1';
    const DIST_ANC_HOR = 'distAncHor';
    const DIST_ANC_VER = 'distAncVer';
    const NUM_ANCHORS = 'refAncoras';
    const ELLIPSE_WIDTH = 'elpLargura';
    const ELLIPSE_HEIGHT = 'elpAltura';
    const OUTPUT_FORMAT = 'formatoSaida';
    const VALIDATE_MASK = 'validaReconhecimento';

    /**
     * @var static string $staticDir Caminho para diretório contendo as imagens das âncoras.
     */
    protected static $staticDir = __DIR__ . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR;
    /**
     * @var string $name Caminho completo para a máscara a ser carregada
     */
    protected $name;
    /**
     * @var string $type Tipo de manipulador de imagem que de ser usado. 
     *      Tipos possíveis definidos em ImageFactoty.
     */
    protected $type;
    /**
     * @var int[] Ponto central da primiera âncora da máscara.
     */
    private $startPoint;
    /**
     * @var $distAncHor Distância vertical entre as âncoras
     */
    private $distAncHor;
    /**
     * @var $distAncVer Distância horizontal entre as âncoras
     */
    private $distAncVer;
    /**
     * @var mixed[] $regions @todo documentar. Link para forma de criação!
     */
    private $regions;
    /**
     * @var int $numAnchors Quantidade de âncoras sendo utilizada para definir
     *      um ponto no template
     */
    private $numAnchors = 1;
    /**
     * @var string $formatOutput @todo documentar. Link para forma de criação!
     */
    private $formatOutput = false;
    /**
     * @var Image[] $anchors @todo documentar
     */
    private $anchors = []; 
    /**
     * Alturada das elipses contidas na imagem
     * @todo está medida deveria servir somente como valor default, a definição do tamanh
     *      da elipse  dentro da região deve sobreescrever este valor.
     */   
    private $ellipseWidth;
    /**
     * Alturada das elipses contidas na imagem
     * @todo está medida deveria servir somente como valor default, a definição do tamanh
     *      da elipse  dentro da região deve sobreescrever este valor.
     */
    private $ellipseHeight;
    /**
     * @var array $outputFormat deve ser um dicionário tendo como chave o nome
     * esperado pra saída (qualquer nome) e como valor ou uma string ou 
     * um array. Caso seja string, deve ser igual ao ID de alguma região
     * do template. Caso seja array, deve obrigatoriamente conter um
     * índice de chave 'match' o qual possui a expressão regular que será
     * usada como filtro para os ID's das regiões. Somente ID's que passarem
     * na comparação com 'match' serão incluídos na saída. O resultado das
     * diversas regiões que tiverem match serão concatenados, opcionalmente
     * é informar uma função de ordenação usando o índice 'sort'. Abaixo um
     * exemplo de formato de arquivo válido:
     * [
     *   'ausente' => 'eAusente', // Serve somente como alias
     *   'respostas' => [         // Concatena todos os resultadas de regiões com
     *    'match' => '/^e-/',     // ID que passe no condição definida em match
     *     'sort' => function($a,$b){
     *       return $a > $b;          
     *     },
     *   ],
     * ];
     * O formato acima terá como saída duas linhas (ausente e respostas). A primeira
     * linha tem o valor interpretado pela região de ID 'eAusente' a segunda linha 
     * terá o resultado concatenado de todas as regiões que tenham ID que comece com 'e-'.
     */
    private $outputFormat = [];
    /**
     * @var array $validateMask Definição da região e do valor a ser usado para validar
     * aplicação do template.
     */
    private $validateMask = false;

    /**
     * Armazena nome do arquivo de máscara em uso.
     *
     * @param string $name Caminho completo para a máscara a ser carregada
     * @param string $type Tipo de manipulador de imagem que de ser usado para carregamento
     *      das imagens das âncoras
     */
    public function __construct($name, $type = ImageFactory::GD)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Abre e interpreta arquivo JSON com as definições do template.
     *
     * @throws Exeception Quando START_POINT,DIST_ANC_HOR ou DIST_ANC_VER não for informado.
     */
    public function load()
    {
        $extension = pathinfo($this->name,PATHINFO_EXTENSION);
        if ($extension !== 'json') {
            throw new \Exception("Arquivo deve ser JSON.");
        }
        if (is_readable($this->name)) {
            $str = file_get_contents($this->name);
            $data = json_decode($str,true);

            if (isset($data[self::START_POINT])) {
                $this->startPoint = $data[self::START_POINT];
            } else {
                throw new Exception("Localização da primeira âncora deve ser informada. Use " . self::START_POINT);
            }
            
            if (isset($data[self::DIST_ANC_HOR])) {
                $this->distAncHor = $data[self::DIST_ANC_HOR];
            } else {
                throw new Exception("Distância vertical entre as âncoras. Use " . self::DIST_ANC_HOR);
            }

            if (isset($data[self::DIST_ANC_VER])) {
                $this->distAncVer = $data[self::DIST_ANC_VER];
            } else {
                throw new Exception("Distância horizontal entre as âncoras. Use " . self::DIST_ANC_VER);
            }

            if (isset($data[self::REGIONS])) {
                $this->regions = $data[self::REGIONS];
            }

            if (isset($data[self::FORMAT_OUTPUT])) {
                $this->formatOutput = json_decode($data[self::FORMAT_OUTPUT], true);
            }

            if (isset($data[self::NUM_ANCHORS])) {
                $this->numAnchors = $data[self::NUM_ANCHORS];
            }

            if (isset($data[self::ELLIPSE_WIDTH])) {
                $this->ellipseWidth = $data[self::ELLIPSE_WIDTH];
            }

            if (isset($data[self::ELLIPSE_HEIGHT])) {
                $this->ellipseHeight = $data[self::ELLIPSE_HEIGHT];
            }

            if (isset($data[self::OUTPUT_FORMAT])) {
                $this->outputFormat = json_decode($data[self::OUTPUT_FORMAT], true);
            }            

            if (isset($data[self::VALIDATE_MASK])) {
                $this->validateMask = json_decode($data[self::VALIDATE_MASK]);
                if (!is_array($this->validateMask)) {
                    $this->validateMask = false;
                }
            }            

            $this->loadAnchors();

        } elseif (file_exists($this->name)) {
            throw new \Exception("Sem permissão de leitura no arquivo '{$this->name}'.");
        } else {
            throw new \Exception("Arquivo '{$this->name}' não encontrado ou não existe.");
        }
        return $this;
    }

    /**
     * Carrega âncoras da máscaera
     * @todo permitir definição de tipo e quantidade de âncoras
     */
    protected function loadAnchors()
    {
        for ($i = 1; $i < 5; $i++) {
            $imageName = self::$staticDir . "ancora{$i}.jpg"; 
            $this->anchors[$i] = ImageFactory::create($imageName, $this->type);
            $this->anchors[$i]->load();
        }
    }

    /**
     * @return valor de @var $startPoint
     */
    public function getStartPoint()
    {
        return $this->startPoint;        
    }

    /**
     * @return valor de @var $distAncHor
     */
    public function getHorizontalDistance()
    {
        return $this->distAncHor;        
    }

    /**
     * @return valor de @var $distAncVer
     */
    public function getVerticalDistance()
    {
        return $this->distAncVer;
    }

    /**
     * @return as regiões da máscara
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @return a quantidade de âncora utilizadas para definir um ponto
     */
    public function getNumAnchors()
    {
        return $this->numAnchors;
    }

    /**
     * @return a largura da elipse
     */
    public function getEllipseWidth()
    {
        return $this->ellipseWidth;
    }

    /**
     * @return a altura da elipse
     */
    public function getEllipseHeight()
    {
        return $this->ellipseHeight;
    }

    /**
     * @return o padrão de formatação de saída
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * @return o padrão de validação para aplicação do template.
     */
    public function getValidateMask()
    {
        return $this->validateMask;
    }

    /**
     * Retorna a assinatura da âncora $anchor. 
     *
     * @throws Exception Caso nenhum objeto ou mais de um seja retornado. 
     *
     * @return array @todo confirmar formato da busca
     */
    public function getSignatureOfAnchor($anchor)
    {
        if (!isset($this->anchors[$anchor])) {
            throw new \Exception("Âncora {$anchor} não definida no template.");
        }
        $objects = $this->anchors[$anchor]->getAllObjects(Tarsius::$minArea, Tarsius::$maxArea);
        if (count($objects) != 1) {
            throw new \Exception("Assinatura da Âncora {$anchor} não pode ser gerada.");
        }

        return $objects[0]->getSignature();
    }

    /**
     * Retorna a assinatura da primeira âncora. A âncora
     * superior esquerda é considerada como primeira.
     */
    public function getSignatureAnchor1()
    {
        return $this->getSignatureOfAnchor(1);
    }

    /**
     * Retorna a assinatura da segunda âncora. A âncora
     * superior direita é considerada como segunda.
     */
    public function getSignatureAnchor2()
    {
        return $this->getSignatureOfAnchor(2);
    }

    /**
     * Retorna a assinatura da terceira âncora. A âncora
     * inferior direita é considerada como terceira.
     */
    public function getSignatureAnchor3()
    {
        return $this->getSignatureOfAnchor(3);
    }

    /**
     * Retorna a assinatura da quarta âncora. A âncora
     * inferior esquerda é considerada como quarta.
     */
    public function getSignatureAnchor4()
    {
        return $this->getSignatureOfAnchor(4);
    }
}