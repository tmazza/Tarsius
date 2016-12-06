<?php
bcscale(14);

define('DEBUG',false);

define('CORTE_PRETO', 128);
define('TOLERANCIA_MATCH', 0.4); # eg: areabase  = 1000. busca triangulos de area entre 500 e 1500
define('EXPANSAO_BUSCA', 0.4); # taxa de aumento da área de busca
define('QTD_EXPANSOES_BUSCA', 5);

include __DIR__.'/Buscador.php';
include __DIR__.'/BuscarAncoras.php';
include __DIR__.'/Objeto.php';
include __DIR__.'/Helper.php';
include __DIR__.'/ConnectedComponent.php';
include __DIR__.'/Assinatura.php';
include __DIR__.'/AnalisarRegioes.php';
// include __DIR__.'/OCR_teste.php';
// include __DIR__.'/Barcode.php';

/**
 * Description of Image
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
    public $escala; // Quantidade de pixel por mm
    public $ancoras = array();
    public $rot = 0; // em radianos
    public $template;
    public $resolucao = 300; # Em dpi
    public $formatoSaida = false;

    public $output = array();

    public $coefA;
    public $coefB;
    
    public $minMatchAncora;
    public $validaTemplate = true;

    public function __construct($template,$preenchimentoMinimo=0.3,$minMatchAncora=0.85) {
      $this->template = $template;
      $this->preenchimentoMinimo = $preenchimentoMinimo; 
      $this->minMatchAncora = $minMatchAncora;
    }

    private function depoisDeDefinirResolucao(){
      $this->escala = bcdiv($this->resolucao,25.4);
      $this->buscador = new Buscador; #Instancia buscador de Objetos
      $this->buscador->minMatch = $this->minMatchAncora;
      $this->assAncoras = $this->loadTemplate($this->template);
      $this->distancias = $this->defineDistancias($this->medidas); #Define distancias baseado na escala inicial
    }

    /**
     * Processa imagem
     */
    public function exec($arquivo,$resolucao=false) {
      $this->timeAll = microtime(true);
      $this->inicializar($arquivo,$resolucao);
      $this->localizarAncoras();

      # atualiza escala com valor avaliado
      $anc1 = $this->ancoras[1]->getCentro();
      $anc4 = $this->ancoras[4]->getCentro();
      $avaliado = Helper::distBC($anc1,$anc4);
      $esperado = $this->medidas['distAncVer'];
      $this->setEscala(bcdiv($avaliado,$esperado));

      // $aaa = microtime(true);
      // $ocr = new Barcode($this);
      // $barcode = $ocr->exec();
      // $this->output['barcode'] = $barcode;
      // echo ' BARCODE: ' . $barcode . "\n";
      // $this->saveTime('barcode', $aaa); # tempo OCR
      
      $this->analisarRegioes();
      $this->organizarSaida();
      $this->saveTime('timeAll', $this->timeAll); # tempo total

      imagedestroy($this->image);
    }

    /**
     * Processa imagem recebendo a posição das âncporas
     */
    public function execComAncoras($arquivo,$pontos,$resolucao=false) {
      $this->inicializar($arquivo,$resolucao);
      $this->setAncoras($pontos);
      $this->analisarRegioes();
      $this->organizarSaida();
      imagedestroy($this->image);
    }

    private function setAncoras($pontos){
      foreach ($pontos as $k => $p) {
        $this->ancoras[$k+1] = new Objeto();
        $this->ancoras[$k+1]->setCentro(array_values($p));        
      }
      # define rotação
      $novaRotacao = atan(Helper::calcCoefReta($this->ancoras[1]->getCentro(), $this->ancoras[4]->getCentro(), true));
      $this->rot = ($this->rot + $novaRotacao) / 2;
    }

    /**
    * Busca imagem e converte para cinza
    */
    protected function inicializar($arquivo,$resolucao){
      if(DEBUG) $time = microtime(true);

      $this->arquivo = $arquivo;
      // $this->resolucao = $resolucao ? $resolucao : $this->getResolucao();
      $this->resolucao = $this->getResolucao();
      $this->depoisDeDefinirResolucao();
      $this->image = Helper::load($arquivo);
      if (!imagefilter($this->image, IMG_FILTER_GRAYSCALE))
        throw new Exception('Imagem não pode ser convertida para tons de cinza.', 500);

      if(DEBUG) $this->saveTime('_inicializar', $time);
    }

    private function getResolucao()
    {
      $a = fopen($this->arquivo,'r');
      $string = fread($a,20);
      fclose($a);
      $data = bin2hex(substr($string,14,4));
      $x = hexdec(substr($data,0,4));
      $y = hexdec(substr($data,4,4));
      return $x == $y ? $x : 300;      
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
        '3' => $this->ancoras[3]->getCentro(),
        '4' => $this->ancoras[4]->getCentro(),
      );

      $this->output['CORTE_PRETO'] = CORTE_PRETO;
      $this->output['MATCH_ANCORA'] = $this->minMatchAncora;
      $this->output['PREENCHIMENTO_MINIMO'] = $this->preenchimentoMinimo;
      $this->output['RESOLUCAO_IMAGEM'] = $this->resolucao;


      # Valida template baseado no valor avaliado de alguma região.
      if($this->validaTemplate && isset($this->medidas['validaReconhecimento']) && $this->medidas['validaReconhecimento']){
        list($regiao,$valorEsperado) = json_decode($this->medidas['validaReconhecimento'],true);

        if(isset($this->output['regioes'][$regiao])){
          $valorAvaliado = $this->output['regioes'][$regiao][0];
          $lcs = $this->LCS($valorEsperado, $valorAvaliado);
          if($lcs < (strlen($valorEsperado)-3) ){
            throw new Exception("Template não reconhecido, valor esperado '$valorEsperado' diferente do valor avaliado '$valorAvaliado'. LCS: '{$lcs}' ", 1);
          }
        } else {
          throw new Exception("Template não reconhecido, região não encontrada.", 1);
        }        
      }

      $saida = array_map(function($i) { return $i[0]; },$this->output['regioes']); 
      $saidaFormatada = $this->formatoSaida ? $this->formatarSaida($this->formatoSaida,$this->output['regioes']) : [];
      $this->output['saidaFormatada'] = array_merge($saida,$saidaFormatada);
    }


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
     * Organiza saída da interpretação das regiões de acordo com o formato de saida.
     * @param formatoSaida deve ser um dicionário tendo como chave o nome
     * esperado pra saída (qualquer nome) e como valor ou uma string ou 
     * um array. Caso seja string, deve ser igual ao ID de alguma região
     * do template. Caso seja function, deve obrigatoriamente conter um
     * índice de chave 'match' o qual possui a expressão regular que será
     * usada como filtro para os ID's das regiões. Somente ID's que passarem
     * na comparação com 'match' serão incluídos na saída. O resultada das
     * diversas regiões que tiverem match serão concatenados, opcionalmente
     * é possível passar um função de ordenação com índice 'sort'. Abaixo um
     * exemplo de formato de arquivo válido:
     * [
     *   'ausente' => 'eAusente', // Serve somente como alias, porque haverá na saída um chave
     *   'respostas' => [         // de nome 'eAusente' com o respectivo valor interpretado.
     *    'match' => '/^e-/', 
     *     'sort' => function($a,$b){
     *       return $a > $b;          
     *     },
     *   ],
     * ];
     * O formato acima terá como saída duas linhas (ausente e respostas). A primeira
     * linha tem o valor interpretado pela região de ID 'eAusente' a segunda linha 
     * terá o resultado concatenado de todas as regiões que tenham ID que comece com 'e-'.
     *
     * @param data dicionário com chave sendo o ID de uma região e chave o valor 
     * interpretado nessa região. Exemplo:
     * [
     *    'e-02' => 'B',
     *    'e-01' => 'A',
     *    'e-03' => 'C',
     *    'eAusente' => 'SIM',
     * ]
     *  
     * @return dicionario com as chaves sendo iguais as definidas em {@param formatoSaida}
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
    protected function formatarSaida($formatoSaida,$data){
      $output = [];
      foreach ($formatoSaida as $key => $value) {
        if(is_string($value)){
          $output[$key] = $data[$value][0];
        } else {

          $matchs = array_filter(array_keys($data),function($i) use($value){
            return preg_match($value['match'],$i) == 1;
          });

          if(isset($value['sort']) && $value['sort']) 
            usort($matchs,$value['sort']);

          # TODO: usar função agrupadora para processar conjunto de registros
          # eg: número do avaliador no canhoto
          $output[$key] = '';
          foreach ($matchs as $m) 
            $output[$key] .= $data[$m][0];

        }
      }
      return $output;
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
        $this->distancias = $this->defineDistancias($this->medidas); 
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

        $templateFile = __DIR__.'/../data/template/' . $template . '/template.json';

        $str = file_get_contents($templateFile);
        $data = json_decode($str,true);

        if(isset($data['formatoSaida'])){
          $this->formatoSaida = json_decode($data['formatoSaida'],true);
          unset($data['formatoSaida']);
        }

        $this->medidas = $data;

        $assinaturas = array();
        for ($i = 1; $i < 5; $i++) {
            $image = Helper::load(__DIR__.'/ancoras/ancora' . $i . '.jpg');
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
