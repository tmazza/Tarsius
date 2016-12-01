<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ConnectedComponent;

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
     */ 
    const THRESHOLD = 128;

    /**
     * @var string $name Nome completo do arquivo sendo manipulado.
     */
    public $name;
    /**
     * @var resource $image Manipulador da imamgem
     */
    protected $image;
    /**
     * @var int $resolution Resolução extraída dos meta dados da imagem.
     */
    private $resolution = false;

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
        $x0 = $x0 >= 0 ? $x0 : 0; // Não ultrapassa limites da imagem
        $y0 = $y0 >= 0 ? $y0 : 0; // Não ultrapassa limites da imagem

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
     * @todo retornar a assinatura da imagem?região
     */
    public function getSignature()
    {
        return [];
    }

}