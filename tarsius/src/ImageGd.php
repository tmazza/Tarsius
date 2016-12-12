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
    public function load()
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
    public function isBlack($x, $y)
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
        return (ceil(0.299*$r) + ceil(0.587*$g) + ceil(0.114*$b)) < Tarsius::$threshold;
    }

    /**
     * Extrai informação de largura da imagem
     */
    public function getWidth()
    {
        if (!$this->width) {
            $this->width = imagesx($this->image);
        }
        return $this->width;
    }

    /**
     * Extrai informação de altura da imagem
     */
    public function getHeight()
    {
        if (!$this->height) {
            $this->height = imagesy($this->image);
        }
        return $this->height;
    }

    /**
     * Cria cópia de pedaço da imagem
     */
    public function cropAndCreate($fileName, $p1, $p2)
    {
        $temp = imagecreatetruecolor($p2[0]-$p1[0], $p2[1]-$p1[1]);
        imagecopy($temp, $this->image, 0, 0, $p1[0], $p1[1], $p2[0], $p2[1]);
        imagejpeg($temp, $fileName);
        imagedestroy($temp);
    }

    /** DEBUG only
     * Função definida em ImageDebug
     */
    public function save($image, $name)
    {
        if (!is_dir(Tarsius::$debugDir)) {
            $old = umask(0);
            mkdir(Tarsius::$debugDir, 0777);
            umask($old);
        }
        $filename = microtime(true) . "_" . rand(0,100) . "_{$name}.png";
        imagepng($image, Tarsius::$debugDir . DIRECTORY_SEPARATOR . $filename);
    }

    /** DEBUG only
     * Função definida em ImageDebug
     */
    public function saveIn($image, $path)
    {
        imagepng($image, $path);
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
    public function drawRectangle($image, $p1, $p2, $rgb = [255, 0, 0])
    {
        list($x1, $y1) = $p1;
        list($x2, $y2) = $p2;
        list($r, $g, $b) = $rgb;
        imagerectangle($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, $r, $g, $b));
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

    /** DEBUG only
     * Escreve texto $text na imagem
     */
    public function writeText(&$image, $text, $startPoint, $fontFamily = false, $fontSize = 15, $angle = 0.0, $rgb = [255, 0, 0])
    {
        list($x, $y) = $startPoint;
        list($r, $g, $b) = $rgb;
        $color = imagecolorallocate($image, $r, $g, $b);
        imagettftext($image, $fontSize, $angle, $x, $y, $color, $fontFamily, $text);
    }
}