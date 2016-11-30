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
            $image = @imagecreatefromjpeg($this->name);
            if (!$image) {
                throw new \Exception("Erro desconhecido ao carregar imagem '{$this->name}'.");
            }
        } elseif (file_exists($this->name)) {
            throw new \Exception("Sem permissão de leitura na imagem '{$this->name}'.");
        } else {
            throw new \Exception("Imagem '{$this->name}' não encontrada ou não existe.");
        }
        return $this;
    }
}