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

    const REGION_ELLIPSE = 0;
    const REGION_NUMERIC_OCR = 1;

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
        $this->findAnchors();
        
        # Atualiza informação de escala considerando distâncias esperada e avaliada entre as âncoras
        $a1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        $a4 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        $observed = $this->distance($a1,$a4);
        $expected = $this->mask->getVerticalDistance();
        $this->setScaleDirect(bcdiv($observed,$expected,14));
        
        # avalia regiões da imagem
        $result = $this->evaluteRegions();
        print_r($result);

        # TODO: organizar saída

    }

    /**
     * Busca âncoras na imagem. Inicia busca no ponto esperado da âncora definido
     * na máscara em uso  A numeração das âncoras é considerada em sentido horário 
     * começando pelo canto superior esquerdo. São necessárias 4 âncoras e essas 
     * devem formar um retângulo.
     *
     * @throws Exception Caso alguma das não seja encontrada
     */
    private function findAnchors()
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
        $p4 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        $this->updateRotation($p1, $p4, true);

        # DEBUG
        if (Tarsius::$enableDebug) {
            $copy = $this->image->getCopy();
            $a1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
            $a2 = $this->anchors[Mask::ANCHOR_TOP_RIGHT]->getCenter();
            $a3 = $this->anchors[Mask::ANCHOR_BOTTOM_RIGHT]->getCenter();
            $a4 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
            $this->image->drawRectangle($copy, $a1, $a3, [0, 255, 0]);
            $this->image->drawRectangle($copy, $a2, $a4, [0, 0, 255]);
            $this->image->save($copy, 'anchor');
        }

    }

    /**
     * Avalia resultado para cada região definida em $mask, de acordo com 
     * o tipo da região e as condições de preenchimento definidas em Tarsius
     *
     * @return array Lista de regiões com ID da região como chave e o resultado
     *      da interpretaçõao e informações relevantes como valor.
     */
    private function evaluteRegions()
    {
        # DEBUG
        $copy = Tarsius::$enableDebug ? $this->image->getCopy() : null;
        
        $result = [];
        $regions = $this->mask->getRegions();
        foreach ($regions as $id => $region) {
            $type = $region[0];
            if ($type == self::REGION_ELLIPSE) {
                $result[$id] = $this->evaluateEllipse($region, $copy);                
            } else {
                throw new \Exception("Tipo de região '{$type}' desconhecido.");
            }
        }

        # DEBUG
        if (Tarsius::$enableDebug) {
            $this->image->save($copy, 'elipses');
        }
        return $result;
    }

    /**
     * Retorna o ponto da região sendo analisada considerando a forma 
     * como o template foi gerado, usando 1, 2 ou as 4 âncoras como referência
     * para cada ponto.
     *
     */
    private function getPointWithCorretion($region)
    {
        if (4 == $this->mask->getNumAnchors()) {
            return $this->getQuadrupleReference($region);
        }
        return $this->getSingleReference($region);
    }

    /**
     * Define posição do ponto considerando sua âncora base.
     */
    private function getSingleReference($region)
    {
        $px = $region[1];
        $py = $region[2];

        # Define âncora base de acordo com o sinal das coordenadas dos pontos
        $base = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        if($px < 0 && $py > 0){
          $base = $this->anchors[Mask::ANCHOR_TOP_RIGHT]->getCenter();
        } elseif($px < 0 && $py < 0){
          $base = $this->anchors[Mask::ANCHOR_BOTTOM_RIGHT]->getCenter();
        } elseif($px > 0 && $py < 0){
          $base = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        }
        # soma âncora base de cada ponto
        $px += $base[0];
        $py += $base[1];
        return $this->rotatePoint([$px,$py], $base, $this->rotation);
    }

    /**
     * Defini posição do ponto usando as quatros âncoras como referência.
     * Utiliza os pares de ancoras 1-3 e 2-4 para reduzir efeitos não planares.
     */
    private function getQuadrupleReference(&$region)
    {
        list($p1,$p3) = $region[1];
        list($p2,$p4) = $region[2];

        $ancora1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        $ancora3 = $this->anchors[Mask::ANCHOR_TOP_RIGHT]->getCenter();
        $ancora2 = $this->anchors[Mask::ANCHOR_BOTTOM_RIGHT]->getCenter();
        $ancora4 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        # soma âncora base de cada ponto
        $p1 = [bcadd($p1[0], $ancora1[0], 14), bcadd($p1[1], $ancora1[1], 14)];
        $p3 = [bcadd($p3[0], $ancora3[0], 14), bcadd($p3[1], $ancora3[1], 14)];
        $p2 = [bcadd($p2[0], $ancora2[0], 14), bcadd($p2[1], $ancora2[1], 14)];
        $p4 = [bcadd($p4[0], $ancora4[0], 14), bcadd($p4[1], $ancora4[1], 14)];
        # normaliza pontos considerando rotação
        $p1 = $this->rotatePoint($p1, $ancora1, $this->rotation);
        $p3 = $this->rotatePoint($p3, $ancora3, $this->rotation);
        $p2 = $this->rotatePoint($p2, $ancora2, $this->rotation);
        $p4 = $this->rotatePoint($p4, $ancora4, $this->rotation);
        # calcula pontos médios entre pares de âncoras
        $p13 = $this->getMidPoint($p1, $p3);
        $p24 = $this->getMidPoint($p2, $p4);
        return $this->getMidPoint($p13, $p24);
    }

    /**
     * Interpreta a região de uma ellipse. Conta a quantidade de pontos contidos na elipse
     * de centro em $center. 
     *
     * @param array $region A definição de cada índice de $region deve ser:
     *      - 0: tipo da região (0 nesse caso)
     *      - 1 e 2: ponto definindo a região (não utilizado aqui)
     *      - 3: saída a ser retornada caso a região preenchida seja maior do que mínima
     *      - 4: saída a ser retornada caso a região preenchida não seja maior do que mínima
     *      - 5: (opcional) preenchimento mínimo. Caso não seja definido será 
     *           utilizado o valor em minMatch
     *      - 6: (opcional) Largura da elipse. Caso não seja definido será utilizado 
     *           o valor da classe Tarsius
     *      - 7: (opcional) Altura da elipse. Caso não seja definido será utilizado o valor 
     *           da classe Tarsius
     *
     */
    private function evaluateEllipse($region, &$copy)
    {
        $center = $this->getPointWithCorretion($this->applyResolutionTo($region));   
        $minMatch = $region[5] ?? Tarsius::$minMatchEllipse;
        $elpWidth = $this->applyResolutionTo($region[6] ?? $this->mask->getEllipseWidth());
        $elpHeight = $this->applyResolutionTo($region[7] ?? $this->mask->getEllipseHeight());


        list($p1, $p2) = $this->image->createRectangle($center, $elpWidth/1.95, $elpHeight/1.95);
        $points = $this->image->getPointsBetween($p1, $p2);

        # DEBUG
        if (Tarsius::$enableDebug) {
            $this->image->drawRectangle($copy, $p1, $p2);
        
            $observedArea = 0;
            foreach ($points as $x => $columns) {
              foreach ($columns as $y => $v) {
                # verifica se um ponto esta dentro ou fora da elipse
                # equação da elipse | ((x-x0)^2 / a^2) + ((y0-y) / b^2), valores de y crescem 'para baixo' na imagem
                if((($x - $center[0])**2 / ($elpWidth/2)**2) + (($center[1]-$y)**2 / ($elpHeight/2)**2) <= 1){
                    $observedArea++;
                    $this->image->setPixel($copy, [$x, $y], [0,255,0]);
                } else {
                    $this->image->setPixel($copy, [$x, $y], [0,0,255]);
                }
              }
            }   
        } else {
            $observedArea = 0;
            foreach ($points as $x => $columns) {
              foreach ($columns as $y => $v) {
                # verifica se um ponto esta dentro ou fora da elipse
                # equação da elipse | ((x-x0)^2 / a^2) + ((y0-y) / b^2), valores de y crescem 'para baixo' na imagem
                if((($x - $center[0])**2 / ($elpWidth/2)**2) + (($center[1]-$y)**2 / ($elpHeight/2)**2) <= 1){
                    $observedArea++;
                }
              }
            }   
        }

        $expectedArea = pi() * ($elpWidth/2) * ($elpHeight/2);
        $fillRate = $observedArea / $expectedArea;

        return [
          $fillRate >= $minMatch ? $region[3] : $region[4],
          $fillRate,
          $center[0], # TODO: mantido para manter compatibilidade, verficar necessidade
          $center[1], # TODO: mantido para manter compatibilidade, verficar necessidade
        ];

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
        $this->scale = bcdiv($resolution, 25.4, 14);
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
        $startPoint = $this->getExpectedAnchorPosition($anchor);

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
    private function getExpectedAnchorPosition(int $anchor)
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