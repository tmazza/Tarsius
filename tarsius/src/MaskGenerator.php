<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\Math;

/**
 * Gera a máscara para imagem definida em $imageName de acordo com os blocos
 * definidos em $config. A imagem deve ter as âncoras alinhadas vertical e horizontalmente
 * formando um retângulo. Será considerada a escala definida nos meta dados da imagem.
 */
class MaskGenerator extends Mask
{
    use Math;

    /**
     * @var string $imageName Caminho completo para imagem
     */
    private $imageName;
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
    public function __construct($name, $imageName, $config)
    {
        set_time_limit(0);
        parent::__construct($name);
        $this->imageName = $imageName;
        $this->image = ImageFactory::create($imageName, ImageFactory::GD);
        $this->image->load();
        $this->config = $config;
        $this->loadAnchors();
    }

    public function generate()
    {
        # Considerada escala definida nos meta dados da imagem
        $this->setScale($this->image->getResolution());

        # Busca centro das âncoras
        $anchors = $this->getCenterAnchors();

        # Gera posição das regiões
        $copy = $this->image->getCopy();
        $regions = [];
        $base = $anchors[self::ANCHOR_TOP_LEFT];
        foreach ($this->config[self::REGIONS] as $block) {
            # bloco de elipses
            if($block['tipo'] == 0) {
                $blocos = $this->gerarBlocos($block, $copy);
                $regions = array_merge($regions, $this->formataRegioes($block, $blocos, $anchors));

                # debug
                foreach ($regions as $id => $r) {
                    list($p1,$p3) = $r[1];
                    list($p2,$p4) = $r[2];

                    # coonvert para pixel
                    list($x1, $y1) = $p1;
                    list($x2, $y2) = $p2;
                    list($x3, $y3) = $p3;
                    list($x4, $y4) = $p4;

                    $p1 = [$x1*$this->scale, $y1*$this->scale];
                    $p2 = [$x2*$this->scale, $y2*$this->scale];
                    $p3 = [$x3*$this->scale, $y3*$this->scale];
                    $p4 = [$x4*$this->scale, $y4*$this->scale];

                    # converte pontos para 

                    $anchor1 = $anchors[Mask::ANCHOR_TOP_LEFT];
                    $anchor2 = $anchors[Mask::ANCHOR_TOP_RIGHT];
                    $anchor3 = $anchors[Mask::ANCHOR_BOTTOM_RIGHT];
                    $anchor4 = $anchors[Mask::ANCHOR_BOTTOM_LEFT];
                    # soma âncora base de cada ponto
                    $p1 = [$p1[0] + $anchor1[0], $p1[1] + $anchor1[1]];
                    $p2 = [$p2[0] + $anchor2[0], $p2[1] + $anchor2[1]];
                    $p3 = [$p3[0] + $anchor3[0], $p3[1] + $anchor3[1]];
                    $p4 = [$p4[0] + $anchor4[0], $p4[1] + $anchor4[1]];

                    $width = $anchor2[0] - $anchor1[0];
                    $height = $anchor3[1] - $anchor2[1];

                    $p13 = $this->getMidPoint($p1, $p3, $width, $height);
                    $p24 = $this->getMidPoint($p2, $p4, $width, $height);
                    list($x, $y)  = $this->getMidPoint($p13, $p24, $width, $height);

                    $this->image->writeText($copy, $id, [$x+2, $y-5], self::$staticDir . 'OpenSans-Regular.ttf');
                }

            } else if($block['tipo'] == 1) { # OCR

                list($x1,$y1) = $block['p1'];
                list($x2,$y2) = $block['p2'];

                $rgb = [72, 61, 139];
                $this->image->drawRectangle($copy, $block['p1'], $block['p2'], $rgb);
                $this->image->writeText($copy, $block['id'], $block['p1'], self::$staticDir . 'OpenSans-Regular.ttf',15,0.0,$rgb);

                $p1 = [($x1 - $base[0])/$this->scale, ($y1 - $base[1])/$this->scale];
                $p2 = [($x2 - $base[0])/$this->scale, ($y2 - $base[1])/$this->scale];

                $regiaoOCR = [
                    $block['id'] => [
                        $block['tipo'],
                        $p1,
                        $p2,
                    ],
                ];
                $regions = array_merge($regions,$regiaoOCR);
            }
        }

        $content = $this->criaArquivoTemplate($regions, $base, $anchors);

        # gera diretório para template
        $templateDir = dirname($this->imageName);
        # grava arquivo de template
        $filename = $templateDir . DIRECTORY_SEPARATOR . 'template.json';
        $h = fopen($filename,'w+');
        fwrite($h, json_encode($content, JSON_PRETTY_PRINT));
        fclose($h);
        # salva imagem de preview do template gerado
        $filename = $templateDir . DIRECTORY_SEPARATOR . 'template.png';
        $this->image->saveIn($copy, $filename);

    }

    /**
     * Define escala em pixel considerando valor da resolução em dpi.
     */
    private function setScale($resolution)
    {
        $this->scale = bcdiv($resolution, 25.4, 14);
    }

    /**
     * Busca as 4 âncoras da imagem. Extrai todos os objetos entre
     * Tarsius::$minArea e Tarsius::$maxArea e compara com cada uma das
     * assinaturas das âncoras da máscara.
     */
    private function getCenterAnchors()
    {
        $sigAnchors = [
            self::ANCHOR_TOP_LEFT => $this->getSignatureOfAnchor(self::ANCHOR_TOP_LEFT),
            self::ANCHOR_TOP_RIGHT => $this->getSignatureOfAnchor(self::ANCHOR_TOP_RIGHT),
            self::ANCHOR_BOTTOM_RIGHT => $this->getSignatureOfAnchor(self::ANCHOR_BOTTOM_RIGHT),
            self::ANCHOR_BOTTOM_LEFT => $this->getSignatureOfAnchor(self::ANCHOR_BOTTOM_LEFT),
        ];

        Tarsius::config(['minArea' => 1000]);
        $objects = $this->image->getAllObjects(Tarsius::$minArea, Tarsius::$maxArea);

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

        return array_map(function($i) { return $i->getCenter(); }, $anchors);
    }

  /**
    * Analiza imagens nas regiões definidas por p1 e p2 formando um retângulo de
    * busca com diagonal p1(topLeft) -> p2(rightBottom). Filtra objetos que não
    * estejam dentro da faizo [minArea,maxArea]. Os objetos mantidos são agrupados
    * conforme definido em colunasPorLinha e agrupaObjetos.
    * @param colunasPorLinha boolean(false) ou integer
    * @param agrupaObjetos  boolean(false) ou integer se false os objetos são agrupados
    * em um único bloco (contador de bloco é sempre 0). Se for definido um valor, serão
    * geradod colunasPorLinha/agrupaObjetos blocos. Por exemplo:
    * O seguinte cenário: 
    *        a a a b b b 
    *        A A A B B B
    * definindo colunasPorLinha = 6 agrupaObjetos = 3 serão gerados 2 blocos:
    * B0:
    *    L0: a a a
    *    L1: A A A
    * B1:
    *    L0: b b b
    *    L1: B B B
    */
    protected function gerarBlocos($block, &$copy)
    {
        
        $p1 = $block['p1'];
        $p2 = $block['p2'];

        $objects = $this->image->getObjectsBetween($p1, $p2, $block['minArea'], $block['maxArea']);
        $centers = array_map(function($i){ 
           return $i->getCenter(); 
        }, $objects);

        # DEBUG
        
        $this->image->drawRectangle($copy, $p1, $p2);
        $this->image->drawObjects($copy, $objects);
        foreach ($centers as $c) {
            $this->image->setPixel($copy, $c, [0, 0, 0]);
        }
        # DEBUG
        
        $fnSortLinhaColuna = function($a,$b){ 
            return $a[1] == $b[1] ? $a[0] >= $b[0] : $a[1] >= $b[1];
        };
        
        $fnSortLinha = function($a,$b){
            return $a[0] >= $b[0];
        };

        # Ordena da esquerda para direita
        usort($centers, $fnSortLinhaColuna);

        # separa linhas
        if ($block['colunasPorLinha']) {
            $lines = array_chunk($centers,$block['colunasPorLinha']);
        } else {
            $lines = [$centers];
        }

        # Separa cada linha em blocos
        if($block['agrupaObjetos']){
          
            $lines = array_map(function($i) use($block, $fnSortLinha) {
                usort($i, $fnSortLinha);
                return array_chunk($i, $block['agrupaObjetos']);
            },$lines);

            $blocks = [];
            foreach ($lines as $k => $l) {
              foreach ($l as $k2 => $b) {
                if (!isset($blocks[$k2])) {
                  $blocks[$k2]=[];
                }
                $blocks[$k2][] = $b;
              }
            }

        } else {
          $blocks = [$lines];

        }

        return $blocks;
    }

    /**
     * Gera lista de regiões a serem interpretadas. Baseado na configuração do bloco,
     * formata a saída de acordo com o tipo da região sendo criada. Parametros para os tipos: 
     *  - elipse: 
     *      | @param casoTrue string ou function sendo que funcao deve OBRIGATORIAMENTE receber
     *        3 parâmetros, sendo o contador de bloco, o contador de linha e o contador de objeto
     *      | @param casoFalse string ou function sendo que funcao equivalente ao casoTrue
     *      
     */
    protected function formataRegioes($block, $blocos, $anchors)
    {
        $regioes = [];
        foreach ($blocos as $cBloco => $lista) {
            foreach ($lista as $cLinha => $l) {
                $count = 0;
                foreach ($l as $cObjeto => $c) {
                    $anchor1 = $anchors[self::ANCHOR_TOP_LEFT];
                    $anchor2 = $anchors[self::ANCHOR_TOP_RIGHT];
                    $anchor3 = $anchors[self::ANCHOR_BOTTOM_RIGHT];
                    $anchor4 = $anchors[self::ANCHOR_BOTTOM_LEFT];

                    # Converte para milimetros
                    $p1 = [($c[0] - $anchor1[0])/$this->scale,($c[1] - $anchor1[1])/$this->scale];
                    $p3 = [($c[0] - $anchor3[0])/$this->scale,($c[1] - $anchor3[1])/$this->scale];

                    $p2 = [($c[0] - $anchor2[0])/$this->scale,($c[1] - $anchor2[1])/$this->scale];
                    $p4 = [($c[0] - $anchor4[0])/$this->scale,($c[1] - $anchor4[1])/$this->scale];

                    $genId = $block['id'];
                    $idRegiao = is_string($genId) ? $genId : $genId($cBloco,$cLinha,$cObjeto);
                    $regioes[$idRegiao] = $this->formataTipoRegiao($block, [$p1,$p3],[$p2,$p4], $cBloco, $cLinha, $cObjeto);
                    $count++;
                }
            }
        }
        return $regioes;
    }

    /**
     * Monta lisda com parâmetros da região de acordo com seu tipo.
     */
    protected function formataTipoRegiao($cb, $x, $y, $cBloco, $cLinha, $cObjeto)
    {
        $tipo = $cb['tipo'];

        $regiao = [$tipo,$x,$y];

        if($tipo == 0) { # elipse
          $casoTrue = $cb['casoTrue'];
          $casoFalse = $cb['casoFalse'];
          $regiao[] = is_string($casoTrue) ? $casoTrue : $casoTrue($cBloco,$cLinha,$cObjeto);
          $regiao[] = is_string($casoFalse) ? $casoFalse : $casoFalse($cBloco,$cLinha,$cObjeto);
          $regiao[] = Tarsius::$minMatchEllipse;
        }

        return $regiao;
    }


   /**
    * Geração do arquivo que será utilizado para interpretação das images,
    * arquivo possui as coordendas de cada um da regiões interpretadas junto
    * junto com a fomatação da saída e os valores necessários para interpretação
    * da folha. 
    */
    protected function criaArquivoTemplate($regions, $base, $anchors)
    {

        list($hor,$ver) = $this->getDistanciaAncoras($anchors);

        $startPoint = [$base[0]/$this->scale, $base[1]/$this->scale];
        
        $outputFormat = isset($this->config[self::OUTPUT_FORMAT]) ? json_encode($this->config[self::OUTPUT_FORMAT]) : false;
        $validateMask = isset($this->config[self::VALIDATE_MASK]) ? json_encode($this->config[self::VALIDATE_MASK]) : false;

        $content = [
            self::START_POINT       => $startPoint,
            self::DIST_ANC_HOR      => $hor,
            self::DIST_ANC_VER      => $ver,
            self::ELLIPSE_HEIGHT    => 2.5,     # TODO: obter da médias das regiões do tipo elipse? ou solicitar?
            self::ELLIPSE_WIDTH     => 4.36,    # TODO: obter da médias das regiões do tipo elipse? ou solicitar?
            self::REGIONS           => $regions,
            self::NUM_ANCHORS       => isset($this->config[self::NUM_ANCHORS]) ? $this->config[self::NUM_ANCHORS] : 4,
            self::OUTPUT_FORMAT     => $outputFormat,
            self::VALIDATE_MASK     => $validateMask,
        ];

        return $content;
    }

    private function getDistanciaAncoras(&$anchors)
    {
        $a1 = $anchors[self::ANCHOR_TOP_LEFT];
        $a2 = $anchors[self::ANCHOR_BOTTOM_RIGHT];
        $a4 = $anchors[self::ANCHOR_BOTTOM_LEFT];
        $hor = ($a2[0] - $a1[0]) / $this->scale;
        $ver = ($a4[1] - $a1[1]) / $this->scale;
        
        return [$hor, $ver];
    }
}