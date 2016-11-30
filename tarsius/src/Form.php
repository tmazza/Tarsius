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
     * @var IImage objeto da imagem sendo processado
     */
    private $image;
    /**
     * @var Mask objeto da máscara sendo utilizada para o processar a imagem.
     * A máscara deve conter informações sobre as regiões que devem ser analisadas.
     *
     * @todo linkar para texto explicando como o template deve ser definido
     *
     */
    private $mask;

    /**
     * Procesa a imagem $imageName com o máscara $maskName.
     * 
     * @param string $imageName Nome da imagem a ser processada.
     * @param string $maskName  Nome da máscara que deve ser aplicada na imagem.
     */
    public function __construct(string $imageName, string $maskName) :void
    {
        $this->image = ImageFactory::create(ImageFactory::GD);
        $this->mask = new Mask();
    }

}