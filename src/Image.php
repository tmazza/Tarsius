<?php
define('DEBUG',true);

define('CORTE_PRETO', 150);
define('MATCH_ANCORA', 0.90);

define('TOLERANCIA_MATCH', 0.4); # eg: areabase  = 1000. busca triangulos de area entre 500 e 1500
define('EXPANSAO_BUSCA', 0.4); # taxa de aumento da área de busca
define('QTD_EXPANSOES_BUSCA', 5);

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
class Image {

    private $timeAll;
    public $arquivo;
    public $image;
    public $buscador;
    private $times = array();
    public $assAncoras = array();
    public $medidas = array();
    public $distancias = array();
    public $escala = 12; // Quantidade de pixel por mm
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
      $this->localizarAncoras();

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
      $this->organizaSaida();


      $this->saveTime('timeAll', $this->timeAll); # tempo total

      imagedestroy($this->image);
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
     * Localiza as ancoras da imagem (Sempre tri)
     * @throws Exception
     */
    private function localizarAncoras() {
      $buscarAncoras = new BuscarAncoras($this);
      $buscarAncoras->exec();
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

    private function organizaSaida(){
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
