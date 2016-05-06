<?php
bcscale(14);

define('DEBUG',false);

define('CORTE_PRETO', 150);
define('TOLERANCIA_MATCH', 0.4); # eg: areabase  = 1000. busca triangulos de area entre 500 e 1500
define('EXPANSAO_BUSCA', 0.4); # taxa de aumento da área de busca
define('QTD_EXPANSOES_BUSCA', 5);

define('MATCH_ANCORA', 0.85);
define('PREENCHIMENTO_MINIMO', 0.33);
// define('RESOLUCAO_IMAGEM', 300); # EM DPI

include __DIR__.'/Buscador.php';
include __DIR__.'/BuscarAncoras.php';
include __DIR__.'/Objeto.php';
include __DIR__.'/Helper.php';
include __DIR__.'/ConnectedComponent.php';
include __DIR__.'/Assinatura.php';
include __DIR__.'/AnalisarRegioes.php';
include __DIR__.'/OCR.php';
include __DIR__.'/OCR_teste.php';
include __DIR__.'/Barcode.php';

/**
 * Description of ProcessaImagem
 *
 * @author tiago.mazzarollo
 */
class Image {

    protected $timeAll;
    public $arquivo;
    public $image;
    public $buscador;
    private $times = array();
    public $assAncoras = array();
    public $medidas = array();
    public $distancias = array();
    // public $escala = 11.811024; // Quantidade de pixel por mm
    public $escala; // Quantidade de pixel por mm
    public $ancoras = array();
    public $rot = 0; // em radianos
    private $template;

    //
    public $resolucao = 300; # Em dpi

    public $output = array();

    public $coefA;
    public $coefB;

    /**
     * Nova imagem
     * @param type $file
     */

    public function __construct($template = 'FolhaA5_75') {
      $this->template = $template;
    }

    private function depoisDeDefinirResolucao(){
      $this->escala = bcdiv($this->resolucao,25.4);
      $this->buscador = new Buscador; #Instancia buscador de Objetos
      $this->assAncoras = $this->loadTemplate(  $this->template);
      $this->distancias = $this->defineDistancias($this->medidas); #Define distancias baseado na escala inicial
    }

    /**
     * Processa imagem
     */
    public function exec($arquivo) {
      $this->timeAll = microtime(true);
      $this->inicializar($arquivo);

      // PARTE TESTE PERSPECTIVA
      # valores antes da escala ser alterada
      // $a1 = $this->distancias['ancora1'];
      // $hor = $this->distancias['distAncHor'];
      // $ver = $this->distancias['distAncVer'];
      // $a2 = [$a1[0]+$hor,$a1[1]];
      // $a3 = [$a1[0],$a1[1]+$ver];
      // $base  = [$a1,$a2,$a3];
      // END PARTE TESTE PERSPECTIVA

      $this->localizarAncoras();

      // TESTE PERSPECTIVA
      // $avaliado  = [$this->ancoras[1]->getCentro(),$this->ancoras[2]->getCentro(),$this->ancoras[3]->getCentro()];
      //
      // $mA = [
      //   # yi,Xi,1
      //   [$base[0][1],$avaliado[0][0],1],
      //   [$base[1][1],$avaliado[1][0],1],
      //   [$base[2][1],$avaliado[2][0],1],
      // ];
      //
      // $mB = [
      //   # xi,Xi,1
      //   [$base[0][0],$avaliado[0][0],1],
      //   [$base[1][0],$avaliado[1][0],1],
      //   [$base[2][0],$avaliado[2][0],1],
      // ];
      //
      // $mC = [
      //   # xi,yi,1
      //   [$base[0][0],$base[0][1],1],
      //   [$base[1][0],$base[1][1],1],
      //   [$base[2][0],$base[2][1],1],
      // ];
      //
      // $mD = [
      //   # xi,yi,Xi
      //   [$base[0][0],$base[0][1],$avaliado[0][0]],
      //   [$base[1][0],$base[1][1],$avaliado[1][0]],
      //   [$base[2][0],$base[2][1],$avaliado[2][0]],
      // ];
      // $A =    $this->det($mA);
      // $B = -1*$this->det($mB);
      // $C =    $this->det($mC);
      // $D = -1*$this->det($mD);
      //
      // $X = function($x,$y) use($A,$B,$C,$D){
      //   return (-1*($A*$x+$B*$y+$D)) / $C;
      // };
      // echo $X(235,589);
      // exit;
      //
      // $this->coefA = $this->solve3x3($A,$bx);
      // $this->coefB = $this->solve3x3($A,$by);
      // END TESTE PERSPECTIVA


      // $aaa = microtime(true);
      // $ocr = new OCR($this);
      // // $ocr = new OCR($this);
      // $template = $ocr->exec('code_template');
      // $this->output['template'] = $template;
      // echo 'TEMPLATE: ' . $template . "\n";
      // $this->saveTime('ocr_template', $aaa); # tempo OCR

      // $aaa = microtime(true);
      // $ocr = new Barcode($this);
      // $barcode = $ocr->exec();
      // $this->output['barcode'] = $barcode;
      // echo ' BARCODE: ' . $barcode . "\n";
      // $this->saveTime('barcode', $aaa); # tempo OCR
      $this->analisarRegioes();
      $this->organizarSaida();

      // echo "ROT: " . $this->rot . "\n";

      # testes - desenha retangulo
      // $copia = Helper::copia($this->image);
      // list($x0,$y0) = $this->ancoras[1]->getCentro();
      // list($x1,$y1) = Helper::rotaciona($this->ancoras[3]->getCentro());
      // list($a2x,$a2y) = Helper::rotaciona($this->ancoras[2]->getCentro());
      // imageline($copia,$x0,$y0,$a2x,$a2y,imagecolorallocate($copia,0,255,0));
      // Helper::rect($copia, $x0, $y0, $x1, $y1, 'asdasd');

      $this->saveTime('timeAll', $this->timeAll); # tempo total

      imagedestroy($this->image);
    }

    # TESTE PERESPECTIVA
    private function solve3x3($A,$b){
      $D  = $this->det($A);
      $Dx = $this->det([$b   ,$A[1],$A[2]]);
      $Dy = $this->det([$A[0],$b   ,$A[2]]);
      $Dz = $this->det([$A[0],$A[1],$b   ]);

      return [$Dx/$D,$Dy/$D,$Dz/$D];
    }
    private function det($m){
      list($a1,$a2,$a3) = array_column($m,0);
      list($b1,$b2,$b3) = array_column($m,1);
      list($c1,$c2,$c3) = array_column($m,2);
      return $a1*($b2*$c3-$c2*$b3) - $a2*($b1*$c3-$c1*$b3) + $a3*($b1*$c2-$c1*$b2);
    }
    # FIM TESTE PERSPECTIVA


    /**
    * Busca imagem e converte para cinza
    */
    protected function inicializar($arquivo){

      # TODO: REMOVER ! USAR OS DADOS DA IMAGESM!!!!
      $bitTeste = (int) substr($arquivo,-5,1);
      if($bitTeste == 1 || $bitTeste == 2)
        $this->resolucao = 200;

      $this->depoisDeDefinirResolucao();

      echo 'Resolucao:  ' . $this->resolucao . "<<<--";

      if(DEBUG)
        $time = microtime(true);
      $this->arquivo = $arquivo;
      $this->image = Helper::load($arquivo);
      if (!imagefilter($this->image, IMG_FILTER_GRAYSCALE))
        throw new Exception('Imagem não pode ser convertida para tons de cinza.', 500);
      if(DEBUG)
        $this->saveTime('_inicializar', $time);
    }

    /**
     * Localiza as ancoras da imagem (Sempre tri)
     * @throws Exception
     */
    protected function localizarAncoras() {
      $buscarAncoras = new BuscarAncoras($this);
      $buscarAncoras->exec();
    }

    /**
    * Analisa cada região definida no template de acordo com o tipo especificado
    */
    private function analisarRegioes() {
      $interpretador = new AnalisarRegioes($this);
      $interpretador->exec();
    }

    private function organizarSaida(){
      $this->output['arquivo'] = $this->arquivo;
      $this->output['template'] = $this->template;
      $this->output['escala'] = $this->escala;
      $this->output['rotacao'] = $this->rot;
      $this->output['ancoras'] = array(
        '1' => $this->ancoras[1]->getCentro(),
        '2' => $this->ancoras[2]->getCentro(),
        '3' => $this->ancoras[2]->getCentro(),
        '4' => $this->ancoras[3]->getCentro(),
      );

      $this->output['CORTE_PRETO'] = CORTE_PRETO;
      $this->output['MATCH_ANCORA'] = MATCH_ANCORA;
      $this->output['PREENCHIMENTO_MINIMO'] = PREENCHIMENTO_MINIMO;
      $this->output['RESOLUCAO_IMAGEM'] = $this->resolucao;

    }

    /**
     * Salva tempo decorrido para processar imagem
     * @param type $id
     * @param type $time
     */
    public function saveTime($id, $time) {
        $this->times[$id] = microtime(true) - $time;
    }

    /**
     * Retorna lista de tempos decorridos
     */
    public function getTimes() {
        return $this->times;
    }

    /**
     * Define a escala da imagem.
     * @param type $escala
     */
    public function setEscala($escala) {
        ///
        // $this->escala = $escala;
        // $this->distancias = $this->defineDistancias($this->medidas); # atualiza valor do tempolate de milimetros para pixels!
    }

    /**
     * Busca a assinatura de toda uma imagem
     * @param type $image
     * @return type
     */
    public function getAssinatura($image,$min=200,$max=3000) {
        $pontos = $this->buscador->getPontosDeQuadrado($image, 0, 0, imagesx($image), imagesy($image));
        $objetos = $this->buscador->separaObjetos($pontos, $min, $max);
        return Assinatura::get($objetos[0]);
    }

    /**
     * Carega template e formato das ancoras.
     * TODO: relacionar template com o formato das ancoras!
     */
    private function loadTemplate($template) {
        $this->template = $template;
        $this->medidas = include __DIR__.'/../template/' . $template . '.php';
        $assinaturas = array();
        for ($i = 1; $i < 5; $i++) {
            $image = Helper::load(__DIR__.'/../image/ancoras/ancora' . $i . '.jpg');
            $assinaturas[$i] = $this->getAssinatura($image);
        }
        return $assinaturas;
    }

    /**
     * Obtem as distancias do template da imagem em pixel.
     * Para cada medidad definida em mm multiplica pela escala para obter a distância em pixel;
     */
    private function defineDistancias($medidas) {
        $distancias = array();
        foreach ($medidas as $nome => $medida) {
            if (is_array($medida)) {
                $distancias[$nome] = $this->defineDistancias($medida);
            } else {
                if(gettype ($medida) === 'string'){
                  $distancias[$nome] = $medida;
                } else {
                  $distancias[$nome] = bcmul($medida,$this->escala,14);
                }
            }
        }
        return $distancias;
    }

    public function getRegioes(){
      return $this->distancias['regioes'];
    }


}
