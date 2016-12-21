<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

use Tarsius\ImageFactory;
use Tarsius\Mask;
use Tarsius\Math;
use Tarsius\FormAnalyser;
use Tarsius\Object;

class Form
{
    use Math;

    /**
     * @var string $imageName
     */
    private $imageName;
    /**
     * @var string $maskName
     */
    private $maskName;
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
    public function __construct($imageName, $maskName)
    {
        $this->imageName = $imageName;
        $this->maskName = $maskName;
        $this->image = ImageFactory::create($this->imageName, ImageFactory::GD);
        $this->image->load();
        $this->mask = new Mask($this->maskName);
        $this->mask->load();
    }

    /**
     * Procesa a imagem $imageName utilizando a máscara $maskName.
     *
     * @param array $anchorsPositons Posição das quatro âncoras que devem ser usadas como
     * referência na definição da posição das regiões. Pontos devem ser informados em 
     * pixel.
     */
    public function evaluate($anchorsPositons = false)
    {
        $time = microtime(true);

        # Primeira escala considerada é baseada na resolução extraída dos meta dados da imagem
        $this->setScale($this->image->getResolution());
        
        # Localiza as 4 âncoras da máscara    
        if ($anchorsPositons) {
            $this->loadAnchors($anchorsPositons);
        } else {
            $this->findAnchors();
        }
        
        # Atualiza informação de escala considerando distâncias esperada e avaliada entre as âncoras
        $a1 = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getCenter();
        $a4 = $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->getCenter();
        $observed = $this->distance($a1,$a4);
        $expected = $this->mask->getVerticalDistance();
        $this->setScaleDirect($observed / $expected);

        # Avalia regiões da imagem
        $analyser = new FormAnalyser($this->image, $this->mask, $this->anchors, $this->scale, $this->rotation);
        $detailedResult = $analyser->evaluateRegions();

        # Valida aplicação do template
        $this->validateMask($detailedResult);

        # Monta resultado com as regiões avaliadas e agrupamentos definidos no $outputFormat da máscara
        return $this->completeResults($detailedResult, $time);

    }

    /**
     * Cria objetos das âncoras definindo somente o ponto de massa.
     * 
     */
    private function loadAnchors($anchorsPositons)
    {  
        $this->anchors[Mask::ANCHOR_TOP_LEFT] = new Object();
        $this->anchors[Mask::ANCHOR_TOP_RIGHT] = new Object();
        $this->anchors[Mask::ANCHOR_BOTTOM_RIGHT] = new Object();
        $this->anchors[Mask::ANCHOR_BOTTOM_LEFT] = new Object();
        
        $this->anchors[Mask::ANCHOR_TOP_LEFT]->setCenter($anchorsPositons[Mask::ANCHOR_TOP_LEFT]);
        $this->anchors[Mask::ANCHOR_TOP_RIGHT]->setCenter($anchorsPositons[Mask::ANCHOR_TOP_RIGHT]);
        $this->anchors[Mask::ANCHOR_BOTTOM_RIGHT]->setCenter($anchorsPositons[Mask::ANCHOR_BOTTOM_RIGHT]);
        $this->anchors[Mask::ANCHOR_BOTTOM_LEFT]->setCenter($anchorsPositons[Mask::ANCHOR_BOTTOM_LEFT]);
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
     * Converte milímetros para pixel, considerando a resolução da imagem.
     * @param mixed $data int ou array
     *
     * @return valor(es) em pixel.  
     */
    public function applyResolutionTo($data)
    {
        return $this->applyResolution($data, $this->scale);
    }

    /**
     * Define escala em pixel considerando valor da resolução em dpi.
     */
    private function setScale($resolution)
    {
        $this->scale = $resolution / 25.4;
    }

    /**
     * Define escala considerando valor igual a quantidade de pixel por milímetro.
     */
    private function setScaleDirect($scale)
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
    private function getAnchor($anchor)
    {
        $signature = $this->mask->getSignatureOfAnchor($anchor);
        $startPoint = $this->getExpectedAnchorPosition($anchor);

        $minArea = $maxArea = false;
        if (isset($this->anchors[Mask::ANCHOR_TOP_LEFT])) {
            $area = $this->anchors[Mask::ANCHOR_TOP_LEFT]->getArea();
            $minArea = $area - ($area * Tarsius::$areaTolerance);
            $maxArea = $area + ($area * Tarsius::$areaTolerance);
        }

        $this->anchors[$anchor] = $this->image->findObject($signature, $startPoint, $this->scale, $minArea, $maxArea);
        if ($this->anchors[$anchor] === false) {
            throw new \Exception("Âncora {$anchor} não encontrada.");           
        }
    }

    /**
     * Retorna posição esperada da âncora.
     *
     * @param int $anchor Âncora a ser avaliada
     */ 
    private function getExpectedAnchorPosition($anchor)
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
                throw new \Exception("Operação inválida. Âncora {$anchor} desconhecida.");
        }
    }

    /**
     * Caso a aplicação da validação da máscara esteja definida o valor interpretado 
     * na região especificada será comparado como valor esperado. Se a diferença entre
     * os dois valores for maior do que Tarsius::$templateValidationTolerance uma execeção
     * será lançada. O valor da região e o valor esperado devem ser uma string, a comparaçaõ
     * será feita usando Longest Common Subsequence. 
     *
     * @param array $detailedResult Lista com valores avaliados em cada região
     *
     * @throws Exception Caso a quantidade de diferenças seja maior do que 
     *      Tarsius::$templateValidationTolerance
     */
    private function validateMask(&$detailedResult)
    {
        if ($this->mask->getValidateMask()) {
            list($region, $expectedValue) = $this->mask->getValidateMask();

            if (isset($detailedResult[$region])) {
                $avaliatedValue = $detailedResult[$region][0];
                $lcs = $this->LCS($expectedValue, $avaliatedValue);
                $len = max(strlen($expectedValue), strlen($avaliatedValue));

                if ($lcs < $len-Tarsius::$templateValidationTolerance) {
                    throw new \Exception("Template não reconhecido, " 
                        ."valor avaliado '$avaliatedValue' diferente do esperado '{$expectedValue}'. LCS: '{$lcs}' ");
                }
                // return $lcs; # TODO: salvar nos dados de saída 

            } else {
                throw new \Exception("Região {$region} não definida no template.");
            }
        }
    }

    /**
     * Longest common subsequence problem
     * @link https://en.wikipedia.org/wiki/Longest_common_subsequence_problem link
     */
    private function LCS($a, $b)
    {
      $C = [];
      $m = strlen($a);
      $n = strlen($b);

      for($i=0;$i<=$m;$i++) { if(!isset($C[$i])) $C[$i] = []; $C[$i][0] = 0; }
      for($i=0;$i<=$n;$i++) { $C[0][$i] = 0; }     
      for($i=1;$i<=$m;$i++){
        for($j=1;$j<=$n;$j++){
          if($a[$i-1] == $b[$j-1]){
            $C[$i][$j] = $C[$i-1][$j-1] + 1;
          } else {
            $C[$i][$j] = $C[$i-1][$j] > $C[$i][$j-1] ? $C[$i-1][$j] : $C[$i][$j-1];          
          }
        }
      }
      return $C[$m][$n];
    }

    /**
     * Monta lista com todos as informações do processamento e resultados otidos.
     * 
     * @param array &$detailedResult Lista de resultados obtidos da regiões da máscara
     *
     * @return array Lista informações das parâmetros usados durante o processamento, dos
     *      resultados brutos obtidos e dos resultados formatadas. 
     */
    private function completeResults(&$detailedResult, $time)
    {

        $regionResult = array_map(function($i) { return $i[0]; }, $detailedResult); 
        $extraResult = $this->formatOutput($regionResult);
        $compiledResult = array_merge($regionResult,$extraResult);

        # Configuração utilizada
        $class = new \ReflectionClass('Tarsius\Tarsius');
        $configuration = $class->getStaticProperties();
        return [
            'totalTime' => microtime(true) - $time,
            'imageName' => $this->imageName,
            'maskName' => $this->maskName,
            'configuration' => $configuration,
            'scale' => $this->scale * 25.4,
            'rotation' => $this->rotation,
            'regionsResult' => $detailedResult,
            'result' => $compiledResult,
            'anchors' => array_map(function($i){ return $i->getCenter(); }, $this->anchors),
        ];

    }

    /**
     * Organiza saída da interpretação das regiões de acordo com o formato de saida.
     *
     * @param array regionResult dicionário com chave sendo o ID de uma região e chave o valor 
     * interpretado nessa região. Exemplo:
     * [
     *    'e-02' => 'B',
     *    'e-01' => 'A',
     *    'e-03' => 'C',
     *    'eAusente' => 'SIM',
     * ]
     *  
     * @return array dicionario com as chaves sendo iguais as definidas em {@param formatoSaida}
     * e valor o resultado do processamento, conforme explicado acima. Por exemplo, usando
     * os valores exemplificados acime de {@param formatoSaida} e {@param data} a saída seria:
     * [
     *  'ausente' => 'SIM',
     *  'respostas' => 'ABC',
     * ]
     *
     * Note que a string 'respostas' está em ordem devido a regra de ordenamento definida.
     * Caso não houvesse, a saída seria 'BAC'.
     *
     */
    protected function formatOutput(&$regionResult){
      $output = [];
      $regionsId = array_keys($regionResult);

      foreach ($this->mask->getOutputFormat() as $key => $value) {
        if (is_string($value)) { # Renomeia saída
          $output[$key] = $regionResult[$value];
        } else {

          $matchs = array_filter($regionsId, function($i) use($value){
            return preg_match($value['match'],$i) == 1;
          });

          # @todo permitir uso de uma função que manipule a lista de
          # valores em $match

          if (isset($value['sort']) && $value['sort']) {
            usort($matchs,$value['sort']);
          }

          $output[$key] = '';
          foreach ($matchs as $regionId){
            $output[$key] .= $regionResult[$regionId];
          }


        }
      }
      return $output;
    }

}