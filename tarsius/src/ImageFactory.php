<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ImageGd;
use Tarsius\ImageMagick;

class ImageFactory
{
    const GD = 'gd';
    const IMAGE_MAGICK = 'imagemagick';

    /**
     * Cria objeto será usado para manipular a imagem.
     *
     * @param string $imageName Caminho completo para a imagem que deve ser carregada.
     * @param string $type      Tipo de manipulador de imagem que de ser usado.
     *
     * @throws Exception Caso o tipo informado não seja válido.
     *
     * @return Image Retorna o objeto criado.
     */
    public static function create($imageName, $type)
    {
        if ($type === self::GD) {
            return new ImageGd($imageName);
        }

        throw new \Exception("Tipo de manipulador de imagem inválido.");
    }

}