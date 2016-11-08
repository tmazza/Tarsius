
<?php

define('DEBUG',true);

define('CORTE_PRETO', 150);
define('TOLERANCIA_MATCH', 0.4); # eg: areabase  = 1000. busca triangulos de area entre 500 e 1500
define('EXPANSAO_BUSCA', 0.4); # taxa de aumento da �rea de busca
define('QTD_EXPANSOES_BUSCA', 10);
define('MATCH_ANCORA', 0.85);

include_once __DIR__.'/Buscador.php';
include_once __DIR__.'/BuscarAncoras.php';
include_once __DIR__.'/Objeto.php';
include_once __DIR__.'/Helper.php';
include_once __DIR__.'/ConnectedComponent.php';
include_once __DIR__.'/ConnectedComponent.php';
include_once __DIR__.'/Assinatura.php';
include_once __DIR__.'/AnalisarRegioes.php';


bcscale(14); # precis�o nos opera��es float

/**
 * Description of ProcessaImagem
 *
 * @author tiago.mazzarollo
 */
class GeraTemplate {

  private $coordNeg = false;
  private $fnSortLinhaColuna;
  private $fnSortLinha;
  #
  public $template;
  public $buscador;
  public $ancoraBase;

  public $escala;
  protected $resolucao = 300;
  
  public $qtdExpansoes;
  public $assAncoras;

  public $closestAncora = false;
  public $ancorasDaImagem = false;

  public $refAncoras = 1; # valores poss�veis: 1,2,4. 1:coordenada basead na ancora 1,2: anc 1 e 3. 4: anc 1e3 e 2e4

  /**
   * Cria lista de objetos em {@param arquivo} baseado nas defini��es de {@param $config}
   * Defini��o de config
   *  - Defini��o bloco definir:
   *     - p1 e p2: dois pontos na imagem que formen um diagonal topLeft -> rightBottom
   *     - colunasPorLinha e agrupaObjetos: explicados em {@link gerarBlocos()}
   *     - minArea e maxArea: �rea m�nima e m�xima para considerar como um objeto da regi�o 
   *     - id: string ou function especifica um identificador unico para cada regiao encontrada
   *     - tipo: poss�veis tipos: elipse, ...
   *          - para tipo elipse:
   *                - casoTrue: string ou function valor retornado caso elipse esteja preenchida
   *                - casoFalse: string ou function valor retornado caso elipse n�o esteja preenchida
   *
   */
  public function gerarTemplate($arquivo,$config,$resolucao=false){
    $resolucao = $resolucao ? $resolucao : $this->resolucao;
    $this->init($arquivo,$resolucao);
    $regioes = [];
    foreach ($config['regioes'] as $cb) { # Configuracao Bloco
      if($cb['tipo'] == 0) { # elipse
        $blocos = $this->gerarBlocos($cb);
        $regioes = array_merge($regioes,$this->formataRegioes($cb,$blocos));
      } else if($cb['tipo'] == 1) { # OCR

        list($x1,$y1) = $cb['p1'];
        list($x2,$y2) = $cb['p2'];

        echo $x1 . ' | ' . $y1 . ' | ' . $x2 . ' | ' . $y2 . "\n";
        $x1 = ($x1 - $this->ancoraBase[0])/$this->escala; # Converte para milimetros
        $y1 = ($y1 - $this->ancoraBase[1])/$this->escala; # Converte para milimetros
        $x2 = ($x2 - $this->ancoraBase[0])/$this->escala; # Converte para milimetros
        $y2 = ($y2 - $this->ancoraBase[1])/$this->escala; # Converte para milimetros

        $regiaoOCR = [
          $cb['id'] => [$cb['tipo'],[$x1,$y1],[$x2,$y2]],
        ];
        $regioes = array_merge($regioes,$regiaoOCR);
      }
    }

    # arquivos de saida (template, debug)
    $baseDir = __DIR__.'/../data/template/' . $config['nome'] . '/';# . strtolower(str_replace(' ','_', $config['nome']));

    $this->criaArquivoTemplate($config,$regioes,$baseDir);
    $this->criaImagensDebug($regioes,$baseDir);
  }  

  /**
   * Gera��o do arquivo que ser� utilizado para interpreta��o das images,
   * arquivo possui as coordendas de cada um da regi�es interpretadas junto
   * junto com a fomata��o da sa�da e os valores necess�rios para interpreta��o
   * da folha. 
   */
  protected function criaArquivoTemplate($config,$regioes,$baseDir){
    $content = [
      'raioTriangulo' => (14 * sqrt(2)) / 2, # diagonal / 2
      'ancora1' => [$this->ancoraBase[0]/$this->escala,$this->ancoraBase[1]/$this->escala],
      'distAncHor' => 126,
      'distAncVer' => 189,
      'elpAltura' => 2.5,
      'elpLargura' => 4.36,
      'regioes' => $regioes,
      'refAncoras' => $this->refAncoras,
      'formatoSaida' => isset($config['formatoSaida']) ? CJSON::encode($config['formatoSaida']) : false,
      'validaReconhecimento' => isset($config['validaReconhecimento']) ? CJSON::encode($config['validaReconhecimento']) : false,
    ];

    $file = $baseDir.'/template.json';
    $h = fopen($file,'w+');
    fwrite($h, json_encode($content,JSON_PRETTY_PRINT));
    fclose($h);
  }

  /**
   * Imagens para visualiza��o do resultado da interpreta��o
   */
  protected function criaImagensDebug($regioes,$baseDir){
    # Posi��es dos objetos e seus labels
    $copia = Helper::copia($this->image);
    $corTex = imagecolorallocate($copia,0,150,255);
    $corObj = imagecolorallocatealpha ($copia,150,255,0,50);

    foreach ($regioes as $id => $r) {
      if($r[0] == 0){#elipse
        if($this->closestAncora){
          $ancoraBase = $this->ancorasDaImagem[$r[5]]->getCentro();
          if($r[5] == 2){
            $corObj = imagecolorallocatealpha ($copia,150,0,255,50);
          } elseif ($r[5] == 3) {
            $corObj = imagecolorallocatealpha ($copia,0,150,255,50);
          } elseif ($r[5] == 4) {
            $corObj = imagecolorallocatealpha ($copia,255,0,150,50);
          }
        } else {
          $ancoraBase = $this->ancoraBase;          
        }

        $x = $r[1]*$this->escala+$ancoraBase[0];
        $y = $r[2]*$this->escala+$ancoraBase[1];

        imagefilledellipse($copia,$x,$y,30,30,$corObj);
        imagettftext ($copia,17.0,0.0,$x-5,$y+5,$corTex,__DIR__.'/SIXTY.TTF',$id);
      } elseif($r[0] == 1){ #ocr
        $p1 = $r[1]; $p2 = $r[2];
        $x1 = $p1[0]*$this->escala + $this->ancoraBase[0];
        $y1 = $p1[1]*$this->escala + $this->ancoraBase[1];
        $x2 = $p2[0]*$this->escala + $this->ancoraBase[0];
        $y2 = $p2[1]*$this->escala + $this->ancoraBase[1];

        imagefilledrectangle($copia,$x1,$y1,$x2,$y2,$corObj);
        imagettftext ($copia,17.0,0.0,$x1+10,$y1+10,$corTex,__DIR__.'/SIXTY.TTF',$id);
      }
    }
    imagejpeg($copia,$baseDir.'/preview.jpg');
    imagedestroy($this->image);
  }


  private function buscaAncoraMaisProxima($regiao){
    $ancoras = $this->getAncoras();
    $closest = 1;

    foreach ($ancoras as $k => $a) {
      $atual = Helper::distBC($ancoras[$closest]->getCentro(),$regiao);
      $novo = Helper::distBC($a->getCentro(),$regiao);
      if($novo < $atual){
        $closest = $k;
      }
    }
    return $closest;
  }

  protected function getAncoras(){
    if(!$this->ancorasDaImagem){
      $buscarAncoras = new BuscarAncoras($this);
      $this->ancorasDaImagem[1] = $buscarAncoras->getAncora(1,[0,0]);
      $this->ancorasDaImagem[2] = $buscarAncoras->getAncora(2,[imagesx($this->image),0]);
      $this->ancorasDaImagem[3] = $buscarAncoras->getAncora(3,[imagesx($this->image),imagesy($this->image)]);
      $this->ancorasDaImagem[4] = $buscarAncoras->getAncora(4,[0,imagesy($this->image)]);
    }
    return $this->ancorasDaImagem;
  }

  /**
   * Gera lista de regi�es a serem interpretadas. Baseado na configura��o do bloco,
   * formata a sa�da de acordo com o tipo da regi�o sendo criada. Parametros para os tipos: 
   *  - elipse: 
   *      | @param casoTrue string ou function sendo que funcao deve OBRIGATORIAMENTE receber
   *        3 par�metros, sendo o contador de bloco, o contador de linha e o contador de objeto
   *      | @param casoFalse string ou function sendo que funcao equivalente ao casoTrue
   *      
   */
  protected function formataRegioes($cb,$blocos){
    $regioes = [];
    foreach ($blocos as $cBloco => $lista) {
      foreach ($lista as $cLinha => $l) {
        $count = 0;
        foreach ($l as $cObjeto => $c) {

          if($this->closestAncora){
            $closest = $this->buscaAncoraMaisProxima($c);
            $ancoraBase = $this->ancorasDaImagem[$closest]->getCentro();
          } else {
            $closest = 1;
            $ancoraBase = $this->ancoraBase;
          }

          $x = ($c[0] - $ancoraBase[0])/$this->escala; # Converte para milimetros
          $y = ($c[1] - $ancoraBase[1])/$this->escala; # Converte para milimetros

          $genId = $cb['id'];
          $idRegiao = is_string($genId) ? $genId : $genId($cBloco,$cLinha,$cObjeto);
          $regioes[$idRegiao] = $this->formataTipoRegiao($cb,$x,$y,$cBloco,$cLinha,$cObjeto,$closest);
          $count++;
        }
      }
    }
    return $regioes;
  }

  /**
   * Monta lisda com par�metros da regi�o de acordo com seu tipo.
   */
  protected function formataTipoRegiao($cb,$x,$y,$cBloco,$cLinha,$cObjeto,$closest){
    $tipo = $cb['tipo'];

    $regiao = [$tipo,$x,$y];

    if($tipo == 0) { # elipse
      $casoTrue = $cb['casoTrue'];
      $casoFalse = $cb['casoFalse'];
      $regiao[] = is_string($casoTrue) ? $casoTrue : $casoTrue($cBloco,$cLinha,$cObjeto);
      $regiao[] = is_string($casoFalse) ? $casoFalse : $casoFalse($cBloco,$cLinha,$cObjeto);
      $regiao[] = $closest;
    }

    return $regiao;
  }

  /**
   * Analiza imagens nas regi�es definidas por p1 e p2 formando um ret�ngulo de
   * busca com diagonal p1(topLeft) -> p2(rightBottom). Filtra objetos que n�o
   * estejam dentro da faizo [minArea,maxArea]. Os objetos mantidos s�o agrupados
   * conforme definido em colunasPorLinha e agrupaObjetos.
   * @param colunasPorLinha boolean(false) ou integer
   * @param agrupaObjetos  boolean(false) ou integer se false os objetos s�o agrupados
   * em um �nico bloco (contador de bloco � sempre 0). Se for definido um valor, ser�o
   * geradod colunasPorLinha/agrupaObjetos blocos. Por exemplo:
   * O seguinte cen�rio: 
   *        a a a b b b 
   *        A A A B B B
   * definindo colunasPorLinha = 6 agrupaObjetos = 3 ser�o gerados 2 blocos:
   * B0:
   *    L0: A A A
   *    L1: A A A
   * B1:
   *    L0: b b b
   *    L1: B B B
   */
  protected function gerarBlocos($cb){
    $pontos = $this->buscador->getPontosDeQuadrado($this->image, $cb['p1'][0],  $cb['p1'][1], $cb['p2'][0],  $cb['p2'][1]);
    $objetos = array_map(function($i){ 
      return $i->getCentro(); 
    },$this->buscador->separaObjetos($pontos, $cb['minArea'], $cb['maxArea']));
    #Helper::pintaObjetos($this->image, $this->buscador->separaObjetos($pontos, $cb['minArea'], $cb['maxArea']));
    usort($objetos,$this->fnSortLinhaColuna); # Ordena por linha-coluna
    
    if($cb['colunasPorLinha']){
      $linhas = array_chunk($objetos,$cb['colunasPorLinha']);
    } else {
      $linhas = [$objetos];
    }

    if($cb['agrupaObjetos']){
      $linhas = array_map(function($i) use($cb){
        usort($i,$this->fnSortLinha);
        return array_chunk($i, $cb['agrupaObjetos']);
      },$linhas);
      $blocos = $this->agrupaObjetos($linhas);
    } else {
      $blocos = [$linhas];
    }
    return $blocos;
  }

  /**
   * Separa linhas em blocos
   */
  private function agrupaObjetos($linhas){
    $blocos = [];
    foreach ($linhas as $k => $l) { // 25 linhas
      foreach ($l as $k2 => $b) { // 4 blocos de 4 
        if(!isset($blocos[$k2]))
          $blocos[$k2]=[];
        $blocos[$k2][] = $b;
      }
    }
    return $blocos;
  }

  /**
   * Define resolu��o, busca �ncoras e instancia fun��o de ordenamento
   */
  protected function init($arquivo,$resolucao){
      ini_set('memory_limit', '2048M');
      $this->resolucao = $resolucao;
      $this->escala = bcdiv($this->resolucao,25.4);
      $this->buscador = new Buscador; #Instancia buscador de Objetos
      $this->buscador->minMatch = 0.85;
      $this->qtdExpansoes = 10;
      $this->assAncoras = [
        1 => $this->getAssinatura(Helper::load(__DIR__.'/ancoras/ancora1.jpg')), # TODO: definir valores de busca de acordo com resolu��o da imagem
        2 => $this->getAssinatura(Helper::load(__DIR__.'/ancoras/ancora2.jpg')),
        3 => $this->getAssinatura(Helper::load(__DIR__.'/ancoras/ancora3.jpg')),
        4 => $this->getAssinatura(Helper::load(__DIR__.'/ancoras/ancora4.jpg')),
      ];
      $this->image = Helper::load($arquivo);
      if (!imagefilter($this->image, IMG_FILTER_GRAYSCALE))
        throw new Exception('Imagem n�o pode ser convertida para tons de cinza.', 500);

      $buscarAncoras = new BuscarAncoras($this);
      $ancora1 = $buscarAncoras->getAncora(1,[0,0]); # busca pela 1� ancora a partir da origem

      $this->ancoraBase = $ancora1->getCentro();

      $this->fnSortLinhaColuna = function($a,$b){ 
        return $a[1] == $b[1] ? $a[0] >= $b[0] : $a[1] >= $b[1];
      };
      $this->fnSortLinha = function($a,$b){
        return $a[0] >= $b[0];
      };
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

}
