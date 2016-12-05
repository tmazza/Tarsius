<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\Image;

class ImageGd extends Image
{
    /**
     * @throws Exception Caso o arquivo não exista ou a extensão seja inválida
     *      ou o processo não tenha permissão de leitura no arquivo.
     */
    public function load(): Image
    {
        $extension = pathinfo($this->name,PATHINFO_EXTENSION);
        if ($extension !== 'jpg') {
            throw new \Exception("Imagem deve ser jpg.");
        }
        if (is_readable($this->name)) {
            $this->image = @imagecreatefromjpeg($this->name);
            if (is_null($this->image)) {
                throw new \Exception("Erro desconhecido ao carregar imagem '{$this->name}'.");
            }
        } elseif (file_exists($this->name)) {
            throw new \Exception("Sem permissão de leitura na imagem '{$this->name}'.");
        } else {
            throw new \Exception("Imagem '{$this->name}' não encontrada ou não existe.");
        }
        return $this;
    }

    /**
     * @todo o que fazer quando pixel não pode ser avaliado?
     * @todo tornar busca das cores do pixel mais eficiente
     * @link http://stackoverflow.com/questions/13791207/better-way-to-get-map-of-all-pixels-of-an-image-with-gd Avaliar
     */
    public function isBlack(int $x, int $y): bool
    {
        $rgb = imagecolorat($this->image, $x, $y);
        if (is_numeric($rgb)) {
            $rgb = [
                ($rgb >> 16) & 0xFF,
                ($rgb >>  8) & 0xFF,
                ($rgb >>  0) & 0xFF,
            ];
        } else {
            $rgb = [255, 255, 255];
        }

        list($r, $g, $b) = $rgb;
        return (ceil(0.299*$r) + ceil(0.587*$g) + ceil(0.114*$b)) < Image::THRESHOLD;
    }

    /**
     * Extrai informação de largura da imagem
     */
    public function getWidth(): int
    {
        if (!$this->width) {
            $this->width = imagesx($this->image);
        }
        return $this->width;
    }

    /**
     * Extrai informação de altura da imagem
     */
    public function getHeight(): int
    {
        if (!$this->height) {
            $this->height = imagesy($this->image);
        }
        return $this->height;
    }

    /** DEBUG only
     * Função definida em ImageDebug
     */
    public function save($image, $name)
    {
        imagepng($image, self::$debugDir  . microtime(true) . "_{$name}.png");
    }


    /** DEBUG only
     * Função definida em ImageDebug
     */
    public function copy($image)
    {
        $copy = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagecopy($copy, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        return $copy;
    }

    /** DEBUG only
     * Função definida em ImageDebug
     */ 
    public function drawRectangle($image, $p1, $p2)
    {
        list($x1, $y1) = $p1;
        list($x2, $y2) = $p2;
        imagerectangle($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, 255, 0, 0));
        return $image;
    }

    /** DEBUG only
     * Função definida em ImageDebug
     */ 
    public function setPixel(&$image, $p1, $rgb = [255, 0, 0])
    {
        list($x, $y) = $p1;
        imagesetpixel($image, $x, $y, imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]));
    }
}