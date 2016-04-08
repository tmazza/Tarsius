<?php
define('DEBUG',true);

define('CORTE_PRETO', 150);
define('MATCH_ANCORA', 2);

define('TOLERANCIA_MATCH', 0.4); # eg: areabase  = 1000. busca triangulos de area entre 500 e 1500
define('EXPANSAO_BUSCA', 2); # taxa de aumento da área de busca
define('QTD_EXPANSOES_BUSCA', 4);

define('PREENCHIMENTO_MINIMO', 0.25); # >=40%

include './src/Buscador.php';
include './src/BuscarAncoras.php';
include './src/Objeto.php';
include './src/Helper.php';
include './src/ConnectedComponent.php';
include './src/Assinatura.php';
include './src/AnalisarRegioes.php';
include './src/OCR.php';
include './src/OCR_teste.php';
include './src/Barcode.php';

/**
 * Description of ProcessaImagem
 *
 * @author tiago.mazzarollo
 */
class GeraTemplate {

    private $timeAll;
    public $arquivo;
    public $image;
    public $buscador;
    private $times = array();
    public $assAncoras = array();
    public $medidas = array();
    public $distancias = array();
    public $escala = 300/25.4; // 300dpi
    public $ancoras = array();
    public $rot = 0; // em radianos
    private $template;

    public $output = array();

    /**
     * Nova imagem
     * @param type $file
     */

    public function __construct($template = 'FolhaA5_75') {
        $this->buscador = new Buscador; #Instancia buscador de Objetos
        $this->assAncoras = $this->loadTemplate($template);
        $this->distancias = $this->defineDistancias($this->medidas); #Define distancias baseado na escala inicial
    }

    /**
     * Processa imagem
     */
    public function exec($arquivo) {
      $this->timeAll = microtime(true);
      $this->inicializar($arquivo);

      $pontos = $this->getPontosDeQuadrado($this->image, 0, 0, imagesx($this->image), imagesy($this->image));
      $objetos = $this->separaObjetos($pontos, 1000, 100000);
      Helper::pintaObjetos($this->image, $objetos);
      $copia = Helper::copia($this->image);
      $pontos = '';
      $ancora1 = array_shift($objetos);
      $ancora1 = $ancora1->getCentro();

      $centros = array_map(function($i){ return $i->getCentro(); },$objetos);

      // Ordena por linha-colune
      usort($centros,function($a,$b){
          if(round($a[0],0) == round($b[0],0)){
            return $a[1] > $b[1];
          } elseif($a[0] > $b[0]){
            return 1;
          } else {
            return -1;
          }
      });

      $minY = 90;
      $maxY = 180;
      $qtdLinhas = 20;
      $qtdColunas = 3;

      // Mantem somente pontos em certa �rea
      $elipses = array_filter($centros,function ($i) use($ancora1,$minY,$maxY){
        $y = ($i[1]-$ancora1[1])/$this->escala;
        echo "---" . $y . "\n";
        return $y > $minY && $y < $maxY;
      });

      // agrupa em colunas de 25 linhas
      $colunas = array_chunk($elipses,$qtdLinhas);

      $linhas = [];
      for($i=0;$i<$qtdLinhas;$i++){
        $linhas[$i] = array_column($colunas,$i);
      }

      $linhas = array_map(function($i){ return array_chunk($i,5); },$linhas);


      $colunas = [];
      for($i=0;$i<$qtdColunas;$i++){
        $colunas[$i] = array_column($linhas,$i);
      }

      foreach ($colunas as $col) { // 4 colunas
        foreach ($col as $l) { // 25 linhas
          $cor = imagecolorallocate($copia,rand(0,255),0,rand(0,255));
          $count = 0;
          foreach ($l as $c) {
            $char = 'a';
            $i = $count;
            while($i>0){
              $char++;
              $i--;
            }
            imagefilledellipse($copia,$c[0],$c[1],50,50,$cor);
            $pontos .= 'array(0,'.(($c[0]-$ancora1[0])/$this->escala).','.(($c[1]-$ancora1[1])/$this->escala).',\''.strtoupper($char).'\',\'W\'),' . "\n";
            $count++;
          }
          $pontos .= "\n";
        }
      }
      imagejpeg($copia,__DIR__.'/../image/asd.jpg');
      // echo '--' . count($objetos);
      imagedestroy($this->image);

      $handle = fopen(__DIR__.'/../image/teste.php','w');
      fwrite($handle,$pontos);
      fclose($handle);

      echo '<pre>';
      print_r($pontos);
      exit;
    }


        /**
         * Identifica objetos em $pontos. Filtra por área mínima e máxima
         * @param type $pontos
         * @param type $min
         * @param type $max
         * @return type
         */
        public function separaObjetos($pontos, $min, $max) {
            $objetosConexos = new ConnectedComponent();
            $objetosConexos->setAreaMinima($min);
            $objetosConexos->setAreaMaxima($max);
            return $objetosConexos->getObjetos($pontos);
        }


    public function getPontosDeQuadrado($img, $x0, $y0, $x1, $y1) {
        $pontos = array();
        $x0 = $x0 >= 0 ? $x0 : 0; // Não ultrapassa 0
        $y0 = $y0 >= 0 ? $y0 : 0; // Não ultrapassa 0

        for ($j = $y0; $j < $y1; $j++) {
            for ($i = $x0; $i < $x1; $i++) {
                list($r, $g, $b) = Helper::getRGB($img, $i, $j);
                $isBlack = (($r < CORTE_PRETO && $g < CORTE_PRETO && $b < CORTE_PRETO));
                if ($isBlack) {
                    $pontos[$i][$j] = true;
                }
            }
        }


        return $pontos;
    }

    /**
    * Busca imagem e converte para cinza
    */
    private function inicializar($arquivo){
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
    * Analisa cada região definida no template de acordo com o tipo especificado
    */
    private function analisarRegioes() {
        $interpretador = new AnalisarRegioes($this);
        $interpretador->pontoBase = $this->ancoras[1]->getCentro();
        $interpretador->regioes = $this->distancias['regioes'];
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
        $this->escala = $escala;
        $this->distancias = $this->defineDistancias($this->medidas); # atualiza valor do tempolate de milimetros para pixels!
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
        $this->medidas = include './template/' . $template . '.php';
        $assinaturas = array();
        for ($i = 1; $i < 5; $i++) {
            $image = Helper::load('./image/ancoras/ancora' . $i . '.jpg');
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
                  $distancias[$nome] = $medida * $this->escala;
                }
            }
        }
        return $distancias;
    }


}
