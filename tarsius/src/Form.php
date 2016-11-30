<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ImageFactory;
use Tarsius\Mask;

class Form
{

    /**
     * @var Image objeto da imagem sendo processado
     */
    private $image;
    /**
     * @var Mask objeto da máscara sendo utilizada para o processar a imagem.
     * A máscara deve conter informações sobre as regiões a serem analisadas.
     * Conforme: 
     * @todo linkar para texto explicando como o template deve ser definido
     *
     */
    private $mask;

    /**
     * Carrega imagem e máscara que devem ser utilizadas.
     * 
     * @param string $imageName Nome da imagem a ser processada.
     * @param string $maskName  Nome da máscara que deve ser aplicada na imagem.
     */
    public function __construct(string $imageName, string $maskName)
    {
        $this->image = ImageFactory::create($imageName, ImageFactory::GD);
        $this->image->load();
        $this->mask = new Mask($maskName);
        $this->mask->load();
    }

    /**
     * Procesa a imagem $imageName utilizando a máscara $maskName.
     *
     */
    public function evaluate()
    {
        $this->image->localizarAncoras();        
    }

    /**
     * Busca âncoras na imagem. Inicia busca no ponto esperado da âncora definido
     * na máscara em uso.
     */
    public function localizarAncoras()
    {

    }

}