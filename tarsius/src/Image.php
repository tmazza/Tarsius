<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ConnectedComponent;
use Tarsius\Tarsius;
use Tarsius\ImageDebug;

/**
 * Contém informações e métodos para manipular a imagem sendo processada.
 */
abstract class Image 
{
    use ImageDebug;

    /**
     * Resolução utilizada caso não seja possível ler as informações da imagem.
     */
    const DEFAULT_RESOLUTION = 300;
    /**
     * @var string $name Nome completo do arquivo sendo manipulado.
     */
    protected $name;
    /**
     * @var resource $image Manipulador da imamgem
     */
    protected $image;
    /**
     * @var int $resolution Resolução extraída dos meta dados da imagem.
     */
    protected $resolution = false;
    /**
     * @var int $width Largura da imagem
     */
    protected $width = false;
    /**
     * @var int $height Largura da imagem
     */
    protected $height = false;

    /**
     * Armazena nome da imagem e carrega arquivo de imagem para memória.
     *
     * @return Image 
     */
    abstract public function load();

    /**
     * Define se ponto da imagem é preto ou branco.
     */
    abstract public function isBlack($x, $y);

    /**
     * Retorna a largura da imagem
     * 
     * @return int Largura da da imagem
     */
    abstract public function getWidth();
    /**
     * Retorna a altura da imagem
     * 
     * @return int Altura da da imagem
     */
    abstract public function getHeight();
    /**
     * Cria arquivo com recorte da imagem
     * 
     * @param string $fileName Nome completo do arquivo que será gerado.
     * @param array $p1 Ponto superior esquerdo da região
     * @param array $p2 Ponto inferior direito da região
     */
    abstract public function cropAndCreate($fileName, $p1, $p2);

    /**
     * Armazena nome do arquivo de imagem.
     *
     * @var string $name Caminho completo para a imagem a ser carregada
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * Extrai resolução do arquivo de imagem. Independente da biblioteca de 
     * imagem sendo utilizada. 
     * Caso não consiga inferir a resolução da imagem irá utilizar DEFAULT_RESOLUTION
     * 
     * @todo notificar quando resolução padrão é utilizada
     *
     * @link http://stackoverflow.com/a/12988682 referência
     */
    public function getResolution()
    {
        if (!$this->resolution) {
            $handle = fopen($this->name,'r');
            $string = fread($handle,20);
            fclose($handle);

            $data = bin2hex(substr($string,14,4));
            $x = hexdec(substr($data,0,4));
            $y = hexdec(substr($data,4,4));

            $this->resolution = $x == $y ? $x : self::DEFAULT_RESOLUTION;      
        }
        return $this->resolution;
    }

    /**
     * Extrai lista de pontos pretos contidos dentro da região definida por $p1 e $p2 
     * (considerado como ponto preto).
     *
     * @param int[] $p1 Ponto superior esquerdo da região que deve ser avaliada.
     * @param int[] $p2 Ponto inferior direito da região que deve ser avaliada.
     *
     * @todo não reprocessar regiões já analisadas. Salvar pontos no estado do objeto
     *       para não precisar extrair da imagem a informação novamente.
     *
     * @return array conjunto de pontos pretos indexados pelo eixo x e y na imagem.
     */
    public function getPointsBetween($p1, $p2)
    {
        list($x0, $y0) = $p1;
        list($x1, $y1) = $p2;
        $points = array();
        $x0 = $x0 >= 0 ? $x0 : 0;
        $y0 = $y0 >= 0 ? $y0 : 0;

        for ($j = $y0; $j < $y1; $j++) {
            for ($i = $x0; $i < $x1; $i++) {
                if ($this->isBlack($i, $j)) {
                    $points[$i][$j] = true;
                }
            }
        }
        return $points;
    }

    /**
     * Extrai conjunto de objetos contidos na região delimitada por $p1 e $p2 e
     * que possuam area entre $minArea e $maxArea.
     *
     * @param int[] $p1 Ponto superior esquerdo da região que deve ser avaliada.
     * @param int[] $p2 Ponto inferior direito da região que deve ser avaliada.
     * @param int $minArea Área mínima para considerar objeto
     * @param int $maxArea Área máxima para considerar objeto
     *
     * @return Object[] conjunto de objetos encontrados
     */
    public function getObjectsBetween($p1, $p2, $minArea, $maxArea)
    {
        $points = $this->getPointsBetween($p1, $p2);

        $connectedComponents = new ConnectedComponent();
        $connectedComponents->setMinArea($minArea);
        $connectedComponents->setMaxArea($maxArea);

        # DEBUG
        if (Tarsius::$enableDebug) {
            # gera imagem bom objetos selecionados dentro da região 
            # definida por $p1 e $p2 em destaque
            $objs = $connectedComponents->getObjects($points);
            $copy = $this->getCopy();
            $this->drawRectangle($copy, $p1, $p2);
            $this->drawObjects($copy, $objs);
            $this->save($copy, 'objs');
            return $objs;
        }

        return $connectedComponents->getObjects($points);
    }

    /**
     * Busca todos os objetos da imagem com área enter $minArea e $maxArea
     *
     * @param int $minArea Área mínima que o objeto deve ter 
     * @param int $minArea Área máxima que o objeto deve ter
     *
     * @return array lista de objetos encontrados
     */
    public function getAllObjects($minArea, $maxArea)
    {
        $p1 = [0, 0];
        $p2 = [$this->getWidth(), $this->getHeight()];
        return $this->getObjectsBetween($p1, $p2, $minArea, $maxArea);
    }

    /**
     * Busca objeto que contenha a assinatura objectSignature, iniciando
     * a busca em uma região com centro em $centralPoint.
     * A largura e a altura da região de busca é definida ...
     * @todo passagem de parâmetros de configuração.
     *
     * @param bool[][] &$objectSignature Assinatura do objeto a ser procurado
     * @param int[] &$centralPoint Ponto central da região de busca
     * @param float $scale Escala em uso pelo formulário. (Pode ser diferente da definida nos meta-dados da imagem) 
     * @param mixed $config int|bool Área mínima para considerar objeto
     * @param mixed $config int|bool Área máxima para considerar objeto
     *
     */
    public function findObject(&$objectSignature, &$centralPoint, $scale, $minArea = false, $maxArea = false)
    {
        $minArea = $minArea ? $minArea : Tarsius::$minArea;
        $maxArea = $maxArea ? $maxArea : Tarsius::$maxArea;
      
        $minMatch = Tarsius::$minMatchObject;
        $searchArea = Tarsius::$searchArea * $scale;
        $maxExpansions = Tarsius::$maxExpansions;
        $expasionRate = Tarsius::$expasionRate;

        $match = false;
        do {

            list($p1, $p2) = $this->getPointsOfRegion($centralPoint, $searchArea);
            $objects = $this->getObjectsBetween($p1, $p2, $minArea, $maxArea);

            foreach ($objects as $object) {
                $similarity = Signature::compare(Signature::generate($object), $objectSignature);
                if ($similarity >= $minMatch) {
                    $match = $object;
                }
            }
            $searchArea *= (1 + $expasionRate);
            $maxExpansions--;

        } while (!$match && $maxExpansions > 0);

        return $match;
    }

    /**
     * Gera pontos que formam um quadrado com centro em $centralPoint e
     * de aresta $sideLength
     *     
     * @param int[] $centralPoint
     * @param type $centralPoint
     * @param type $y0
     *
     * @return array Par de pontos, sendo o primeiro o superior esquerdo
     *      e o segundo o inferior direito
     */
    public function getPointsOfRegion($centralPoint, $sideLength)
    {
        return $this->createRectangle($centralPoint, $sideLength, $sideLength);
    }

    /**
     * Retorna ponto superior esquerdo e inferior direito do quadrado com
     * centro em $centralPoint, formando um quadrado de lago $sideLenght*2
     * Não permite que as coordenadas passem os limites da imagem.
     *
     * @todo não permitir que x1 e y1 estejam fora da imagem. Exceção?
     *
     * @param int[] $centralPoint
     * @param type $centralPoint
     * @param type $y0
     *
     * @return array Par de pontos, sendo o primeiro o superior esquerdo
     *      e o segundo o inferior direito
     */
    public function createRectangle($centralPoint, $width, $height)
    {
      list($x0, $y0) = $centralPoint;
        $x1 = $x0 + $width;
        $y1 = $y0 + $height;
        $x0 -= $width;
        $y0 -= $height;

        if ($x0 < 0) { # se atingir o topo da imagem expande para baixo
            $x1 += abs($x0); 
            $x0 = 0;
        }
        if ($y0 < 0) { # se atingir a borda esquerda da imagem expande para direita
            $y1 += abs($y0);
            $y0 = 0;
        }
        $width = $this->getWidth();
        if ($x1 > $width) { # se atingir borda direita da imamgem ignora expansão
            $x1 = $width;
        }
        $height = $this->getHeight();
        if ($y1 > $height) { # se atingir borda direita da imamgem ignora expansão
            $y1 = $height;
        }
        return [
            [$x0, $y0],
            [$x1, $y1], # @todo antes precisa ser y-1, precisa ainda??
        ];  
    }

}