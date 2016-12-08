<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

class MaskGenerator extends Mask
{
    /**
     * @var Image $image objeto da imagem sendo processado
     */
    private $image;
    /**
     * @var array $config Arquivo de configuração com informações para geração do template.
     *
     */
    private $config;

    /**
     * Carrega imagem e configuração para geração da máscara
     * 
     * @param string $imageName Nome da imagem a ser processada.
     * @param array $config Arquivo para geração do template
     */
    public function __construct(string $imageName, array $config)
    {
        parent::__construct('todo?');
        $this->image = ImageFactory::create($imageName, ImageFactory::GD);
        $this->image->load();
        $this->config = $config;
        $this->loadAnchors();
    }

    public function generate()
    {
        # Primeira escala considerada é baseada na resolução extraída dos meta dados da imagem
        $this->setScale($this->image->getResolution());

        # Busca âncoras comparando com todos os objetos da imagem
        $anchors = $this->getAnchors();

        print_r($this->config);

    }

    /**
     * Define escala em pixel considerando valor da resolução em dpi.
     */
    private function setScale(int $resolution)
    {
        $this->scale = bcdiv($resolution, 25.4, 14);
    }

    /**
     * Busca as 4 âncoras da imagem. Extrai todos os objetos entre
     * Tarsius::$minArea e Tarsius::$maxArea e compara com cada uma das
     * assinaturas das âncoras da máscara.
     */
    private function getAnchors()
    {
        $sigAnchors = [
            self::ANCHOR_TOP_LEFT => $this->getSignatureOfAnchor(self::ANCHOR_TOP_LEFT),
            self::ANCHOR_TOP_RIGHT => $this->getSignatureOfAnchor(self::ANCHOR_TOP_RIGHT),
            self::ANCHOR_BOTTOM_RIGHT => $this->getSignatureOfAnchor(self::ANCHOR_BOTTOM_RIGHT),
            self::ANCHOR_BOTTOM_LEFT => $this->getSignatureOfAnchor(self::ANCHOR_BOTTOM_LEFT),
        ];

        Tarsius::config(['minArea' => 1000]);
        $objects = $this->image->getAllObjects(Tarsius::$minArea, Tarsius::$maxArea);

        // $copy = $this->image->getCopy();
        // $this->image->drawObjects($copy, $objects);
        // $this->image->save($copy, 'teste');

        $anchors = [];
        foreach ($objects as $o) {
            $objectSignature = $o->getSignature();
            foreach ($sigAnchors as $id => $signature) {
                if (!isset($anchors[$id]) 
                    && Signature::compare($signature, $objectSignature) > Tarsius::$minMatchObject) {
                    $anchors[$id] = $o;
                }
            }
        }

        if (count($anchors) != 4) {
            throw new \Exception("Uma ou mais âncoras não foram encontradas.");
        }

        return $anchors;

    }

}