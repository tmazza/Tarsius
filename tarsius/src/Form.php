<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ImageFactory;
use Tarsius\Mask;
use Tarsius\Math;

class Form
{
    use Math;    
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
     * @var int $scale Escala sendo utilizada para aplicação da máscara ao template.
     */
    private $scale;
    /**
     * @var int $rotation Rotação da imagem
     */
    private $rotation = 0;
    /**
     * @var array $anchors Âncoras da imagem
     */
    private $anchors = [];

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
        # Primeira escala considerada é baseada na resolução extraída dos meta dados da imagem
        $this->setScale($this->image->getResolution());
        # Localiza as 4 âncoras da máscara        
        $this->localizarAncoras();
        # Atualiza informação de escala considerando distâncias esperadas e avaliadas entre as âncoras
        $a1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        $a4 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        $observed = $this->distance($a1,$a4);
        $expected = $this->mask->getVerticalDistance();
        $this->setScaleDirect(bcdiv($observed,$expected,14));
        # TODO: analisar regiões
        # TODO: organizar saída


        // #DEGUB!
        // $a3 = $this->anchors[Mask::ANCHOR_BOTTOM_RIGHT]->getCenter();
        // $a2 = $this->anchors[Mask::ANCHOR_TOP_RIGHT]->getCenter();
        // $copy = $this->image->getAnCopy();
        // $this->image->drawRectangle($copy, $a1, $a3);
        // $this->image->drawRectangle($copy, $a2, $a4);
        // $this->image->save($copy, 'ancoras');


    }

    /**
     * Busca âncoras na imagem. Inicia busca no ponto esperado da âncora definido
     * na máscara em uso  A numeração das âncoras é considerada em sentido horário 
     * começando pelo canto superior esquerdo. São necessárias 4 âncoras e essas 
     * devem formar um retângulo.
     *
     * @throws Exception Caso alguma das não seja encontrada
     */
    public function localizarAncoras()
    {
        # Encontra âncoras do topo da folha
        $this->getAnchor(Mask::ANCHOR_TOP_LEFT);
        $this->getAnchor(Mask::ANCHOR_TOP_RIGHT);
        
        # Define rotação considerando primeira distância conhecida
        $p1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        $p2 = $this->anchors[Mask::ANCHOR_TOP_RIGHT]->getCenter();
        $this->setRotation($p1, $p2);

        # Encontra âncoras na base da folha
        $this->getAnchor(Mask::ANCHOR_BOTTOM_RIGHT);
        $this->getAnchor(Mask::ANCHOR_BOTTOM_LEFT);

        # Redefine rotação considerando âncoras com maior distância
        $p2 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        $this->updateRotation($p1, $p2, true);
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

    /**
     * Define escala considerando valor igual a quantidade de pixel por milímetro.
     */
    private function setScaleDirect(float $scale)
    {
        $this->scale = $scale;
    }

    /**
     * Atualiza valor de rotação da imagem considerando ângulo entre dois pontos
     */
    private function setRotation($p1, $p2, $reverse = false)
    {
        $this->rotation = atan($this->lineGradient($p1, $p2, $reverse));
    }

    /**
     * Atualiza valor de rotação da imagem considerando ângulo entre dois pontos
     * fazendo uma média simples com o valor já existente
     */
    private function updateRotation($p1, $p2, $reverse = false)
    {
        $this->rotation = ($this->rotation + atan($this->lineGradient($p1, $p2, $reverse))) / 2;
    }

    /**
     * Busca uma âncora da imagem se baseando na posição espeada.
     *
     * @param int $anchor Âncora sendo procurada
     */
    private function getAnchor(int $anchor)
    {
        $signature = $this->mask->getSignatureOfAnchor($anchor);
        $startPoint = $this->getExpectedPositionAnchor($anchor);

        $config = [];
        if (isset($this->anchors[Mask::ANCHOR_TOP_LEFT])) {
            $area = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getArea();
            $config['minArea'] = $area - ($area * 0.5);
            $config['naxArea'] = $area + ($area * 0.5);
            # TODO: configuração de minMatch!??
        }

        $this->anchors[$anchor] = $this->image->findObject($signature, $startPoint, $this->scale, $config);
        if ($this->anchors[$anchor] === false) {
            throw new Exception("Âncora {$anchor} não encontrada.");           
        }
    }

    /**
     * Retorna posição esperada da âncora.
     *
     * @param int $anchor Âncora a ser avaliada
     */ 
    private function getExpectedPositionAnchor(int $anchor)
    {
        if($anchor !== Mask::ANCHOR_TOP_LEFT){
            $posAnchor1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        }
        $horizontalDistance = $this->applyResolutionTo($this->mask->getHorizontalDistance());
        $verticalDistance = $this->applyResolutionTo($this->mask->getVerticalDistance());

        switch ($anchor) {
            case Mask::ANCHOR_TOP_LEFT:
                return $this->applyResolutionTo($this->mask->getStartPoint());
            case Mask::ANCHOR_TOP_RIGHT: 
                return [
                    $posAnchor1[0] + $horizontalDistance, 
                    $posAnchor1[1],
                ];
            case Mask::ANCHOR_BOTTOM_RIGHT: 
                return $this->rotatePoint([
                    $posAnchor1[0] + $horizontalDistance,
                    $posAnchor1[1] + $verticalDistance,
                ], $posAnchor1, $this->rotation); 
            case Mask::ANCHOR_BOTTOM_LEFT: 
                return $this->rotatePoint([
                    $posAnchor1[0], 
                    $posAnchor1[1] + $verticalDistance,
                ], $posAnchor1, $this->rotation); 
            default:
                throw new Exception("Operação inválida. Âncora {$anchor} desconhecida.");
        }
    }

}