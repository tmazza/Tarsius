<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ConnectedComponent;
use Tarsius\Finder;

/**
 * Contém informações e métodos para manipular a imagem sendo processada.
 */
abstract class Image 
{
    /**
     * Resolução utilizada caso não seja possível ler as informações da imagem.
     */
    const DEFAULT_RESOLUTION = 300;
    /**
     * Corte entre pixel pretos e brancos. 
     * 
     * @todo usar limiar dinâmico
     * @todo possibilitar configuração em tempo de execução
     */ 
    const THRESHOLD = 128;

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
    abstract public function load(): Image;

    /**
     * Define se ponto da imagem é preto ou branco.
     */
    abstract public function isBlack(int $x, int $y): bool;

    /**
     * Retorna a largura da imagem
     * 
     * @return int Largura da da imagem
     */
    abstract public function getWidth(): int;
    /**
     * Retorna a altura da imagem
     * 
     * @return int Altura da da imagem
     */
    abstract public function getHeight(): int;

    /**
     * Armazena nome do arquivo de imagem.
     *
     * @var string $name Caminho completo para a imagem a ser carregada
     */
    public function __construct(string $name)
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
    public function getResolution(): int
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
    public function getPointsBetween(array $p1, array $p2): array
    {
        list($x0, $y0) = $p1;
        list($x1, $y1) = $p2;
        $pontos = array();
        $x0 = $x0 >= 0 ? $x0 : 0;
        $y0 = $y0 >= 0 ? $y0 : 0;

        for ($j = $y0; $j < $y1; $j++) {
            for ($i = $x0; $i < $x1; $i++) {
                if ($this->isBlack($i, $j)) {
                    $pontos[$i][$j] = true;
                }
            }
        }
        return $pontos;
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
    public function getObjectsBetween(array $p1, array $p2, int $minArea, int $maxArea): array
    {
        $pontos = $this->getPointsBetween($p1, $p2);

        $connectedComponents = new ConnectedComponent();
        $connectedComponents->setMinArea($minArea);
        $connectedComponents->setMaxArea($maxArea);

        return $connectedComponents->getObjects($pontos);
    }

    /**
     * Busca todos os objetos da imagem com área enter $minArea e $maxArea
     *
     * @param int $minArea Área mínima que o objeto deve ter 
     * @param int $minArea Área máxima que o objeto deve ter
     *
     * @return array lista de objetos encontrados
     */
    public function getAllObjects(int $minArea, int $maxArea)
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
     * @param int[] $centralPoint Ponto central da região de busca
     */
    public function findObject(array &$objectSignature, array $centralPoint)
    {
        $minArea        = 500;   // @todo passar parâmetros de configuração!
        $maxArea        = 3000;  // @todo passar parâmetros de configuração!
        $searchArea     = 100;   // @todo passar parâmetros de configuração!
        $minMatch       = 0.8;   // @todo passar parâmetros de configuração!
        
        $maxExpansions  = 4;     // @todo passar parâmetros de configuração!
        $expasionRate   = 0.5;   // @todo passar parâmetros de configuração!
        
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
    private function getPointsOfRegion($centralPoint, $sideLength) {
        list($x0, $y0) = $centralPoint;
        $x1 = $x0 + $sideLength;
        $y1 = $y0 + $sideLength;
        $x0 -= $sideLength;
        $y0 -= $sideLength;
        
        if ($x0 < 0) { # se atingir o topo da imagem expande para baixo
            $x1 += abs($x0); 
            $x0 = 0;
        }
        if ($y0 < 0) { # se atingir a borda esquerda da imagem expande para direita
            $y1 += abs($y0);
            $y0 = 0;
        }
        return [
            [$x0, $y0],
            [$x1, $y1], # @todo antes precisa ser y-1, precisa ainda??
        ];
    }

}