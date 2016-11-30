<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\Image;

class ImageGd extends Image
{
    /**
     * Abre e carrega arquivo de imagem para memória.
     *
     * @var string $imageName Caminho completo para a imagem que será carregada
     *
     * @throws Exception Caso o arquivo não exista ou a extensão seja inválida
     *      ou o processo não tenha permissão de leitura no arquivo.
     * 
     * @return void 
     */
    public function load(string $imageName)
    {
        $extension = pathinfo($imageName,PATHINFO_EXTENSION);
        if ($extension !== 'jpg') {
            throw new \Exception("Imagem deve ser jpg.");
        }
        if (is_readable($imageName)) {
            $image = @imagecreatefromjpeg($imageName);
            if (!$image) {
                throw new \Exception("Erro desconhecido ao carregar imagem '{$imageName}'.");
            }
        } elseif (file_exists($imageName)) {
            throw new \Exception("Sem permissão de leitura na imagem '{$imageName}'.");
        } else {
            throw new \Exception("Imagem '{$imageName}' não encontrada ou não existe.");
        }
    }
}