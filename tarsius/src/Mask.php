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

    /**
     * @var static string $anchorsDir Caminho para diretório contendo as imagens das âncoras.
     */
    private static $anchorsDir = __DIR__ . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR;
    /**
     * @var string $name Caminho completo para a máscara a ser carregada
     */
    private $name;
    /**
     * @var string $type Tipo de manipulador de imagem que de ser usado. 
     *      Tipos possíveis definidos em ImageFactoty.
     */
    private $type;
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
     * Armazena nome do arquivo de máscara em uso.
     *
     * @param string $name Caminho completo para a máscara a ser carregada
     * @param string $type Tipo de manipulador de imagem que de ser usado para carregamento
     *      das imagens das âncoras
     */
    public function __construct(string $name, string $type = ImageFactory::GD)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Abre e interpreta arquivo JSON com as definições do template.
     *
     * @throws Exeception Quando START_POINT,DIST_ANC_HOR ou DIST_ANC_VER não for informado.
     */
    public function load(): Mask
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
                throw new Exception("Localização de primeira âncora deve ser informada. Use " . self::START_POINT);
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
                $this->formatOutput = json_decode($data[self::FORMAT_OUTPUT],true);
            }

            if (isset($data[self::NUM_ANCHORS])) {
                $this->numAnchors = $data[self::NUM_ANCHORS];
            }

            /**
             * @todo permitir definição de tipo e quantidade de âncoras
             */
            for ($i = 1; $i < 5; $i++) {
                $imageName = self::$anchorsDir . "ancora{$i}.jpg"; 
                $this->anchors[$i] = ImageFactory::create($imageName, $this->type);
                $this->anchors[$i]->load();
            }

        } elseif (file_exists($this->name)) {
            throw new \Exception("Sem permissão de leitura no arquivo '{$this->name}'.");
        } else {
            throw new \Exception("Arquivo '{$this->name}' não encontrado ou não existe.");
        }
        return $this;
    }

    /**
     * Retorna valor de @var $startPoint
     */
    public function getStartPoint()
    {
        return $this->startPoint;        
    }

    /**
     * Retorna valor de @var $distAncHor
     */
    public function getHorizontalDistance()
    {
        return $this->distAncHor;        
    }

    /**
     * Retorna valor de @var $distAncVer
     */
    public function getVerticalDistance()
    {
        return $this->distAncVer;
    }

    /**
     * Retorna as regiões da máscara
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Retorna a quantidade de âncora utilizadas para definir um ponto
     */
    public function getNumAnchors()
    {
        return $this->numAnchors;
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