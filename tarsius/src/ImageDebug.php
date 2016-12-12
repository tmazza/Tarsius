<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

/**
 * Geração de imagens contendo representação visual do processo sendo realizado a
 * cada etapa
 */
trait ImageDebug
{
    /**
     * Grava uma imagem no diretório de DEBUG definido em Tarsius::$debugDir. 
     * Todas as implementações da função concatenam como prefixo do 
     * nome da imagem o timestamp atual. 
     *
     * @param resource $image Imagem que deve ser gravada
     * @param string $name Nome que deve ser atribuído ao arquivo que será salvo
     *      em Tarsius::$debugDir
     */
    abstract public function save($image, $name);
    /**
     * Grava uma imagem no diretório de definido por $path
     *
     * @param resource $image Imagem que deve ser gravada
     * @param string $name Nome completao para imagem a ser criada
     */
    abstract public function saveIn($image, $path);
    /**
     * Retorna uma cópia de $image
     *
     * @param resource $image imagem que dever ser copiada
     *
     * @return resource Uma cópia de image
     */    
    abstract public function copy($image);
    /**
     * Desenha um retângulo iniciando com canto superior esquerdo em 
     * $p1 e inferior direito em $p2.
     *
     * @param resource Imagem onde será feito o desenho
     * @param array Ponto superior esquerdo do retângulo 
     * @param array Ponto inferior direito do retângulo
     *
     * @return resource Imagem com desenho
     */
    abstract public function drawRectangle($image, $p1, $p2, $rgb = [255, 0, 0]);
    /**
     * Altera a cor do pixel $p1 para $rgb em $image
     *
     * @param resource &$image Imagem a ser manipulada.
     * @param array $p1 Ponto a ser alterado
     * @param array $rgb Cor em formato RGV a ser utilizada.
     */    
    abstract public function setPixel(&$image, $p1, $rgb = [255, 0, 0]);
    /**
     * Escreve $text na imagem, na posição $startPoint com ângulo $angle, font 
     * do tipo $fontFamily e tamanho $fontSize e cor $rgb.
     *
     * @param resource &$image Imagem a ser manipulada.
     * @param string $text 
     * @param float $fontFamily 
     * @param float $fontSize 
     * @param array $startPoint Coordenadas x,y do ponto 
     * @param float $angle 
     * @param array $rgb
     */
    abstract public function writeText(&$image,$text,$startPoint,$fontFamily,$fontSize,$angle,$rgb=[255, 0, 0]);

    /**
     * Retorna uma cópia da imagem em $image
     */
    public function getCopy()
    {
        return $this->copy($this->image);
    }

    /**
     * Pinta os pontos $points da imagem.
     *
     * @param resource &$image Imagem a ser manipulada.
     * @param array $points Conjunto de pontos a serem pintados
     * @param array $rgb Cor em formato RGV a ser utilizada.
     *
     */
    public function drawPoints(&$image, $points, $rgb = [255, 0, 0])
    {
        foreach ($points as $x => $columns) {
            foreach ($columns as $y => $true) {
                $this->setPixel($image, [$x,$y], $rgb);
            }
        }
    }

    /**
     * Pinta o conjunto de objetos da imagem.
     *
     * @param resource &$image Imagem a ser manipulada.
     * @param array $objects Conjunto de objetos a serem pintados
     *
     */
    public function drawObjects(&$image, $objects)
    {
        foreach ($objects as $obj) {
            $points = $obj->getPoints();

            $rgb = [rand(0, 255), rand(0, 255), rand(0, 255)];
            foreach ($points as $p) {
                $this->setPixel($image, $p, $rgb);
            }
        }
    }
}