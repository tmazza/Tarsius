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
     * Escala sendo utilizada para aplicação da máscara ao template.
     */
    private $scale;

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
        $this->localizarAncoras();
    }

    /**
     * Busca âncoras na imagem. Inicia busca no ponto esperado da âncora definido
     * na máscara em uso  A numeração das âncoras é considerada em sentido horário 
     * começando pelo canto superior esquerdo. São necessárias 4 âncoras e essas 
     * devem formar um retângulo.
     */
    public function localizarAncoras()
    {
        # Primeira escala considerada é baseada na resolução extraída dos meta dados da imagem
        $this->setScale($this->image->getResolution());

        # Busca primeira âncora
        $startPoint = $this->applyResolutionTo($this->mask->getStartPoint());
        $signaturaAnchor1 = $this->mask->getSignatureAnchor1();
        $signaturaAnchor2 = $this->mask->getSignatureAnchor2();
        $signaturaAnchor3 = $this->mask->getSignatureAnchor3();
        $signaturaAnchor4 = $this->mask->getSignatureAnchor4();

        Signature::print($signaturaAnchor1);
        Signature::print($signaturaAnchor2);
        Signature::print($signaturaAnchor3);
        Signature::print($signaturaAnchor4);
        echo ' ------ ' . Signature::compare($signaturaAnchor1,$signaturaAnchor2) . "\n";
        echo ' ------ ' . Signature::compare($signaturaAnchor2,$signaturaAnchor3) . "\n";
        echo ' ------ ' . Signature::compare($signaturaAnchor3,$signaturaAnchor4) . "\n";
        echo ' ------ ' . Signature::compare($signaturaAnchor4,$signaturaAnchor1) . "\n";

        // $this->image->find($anchorSignature, $startPoint);
        //print_r($anchorSignature);
    }

    /**
     * Converte milímetros para pixel, considerando a resolução da imagem.
     * @param mixed $data int ou array
     *
     * @return valor(es) em pixel.  
     */
    private function applyResolutionTo($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->applyResolutionTo($v);
            }
        } else {
            $data = bcmul($data,$this->scale,14);
        }
        return $data;
    }

    /**
     * Define escala em pixel considerando valor da resolução em dpi.
     */
    private function setScale(int $resolution)
    {
        $this->scale = bcdiv($resolution, 25.4);
    }

}