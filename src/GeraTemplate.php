<?php

include __DIR__ . '/Image.php';

/**
 * Description of ProcessaImagem
 *
 * @author tiago.mazzarollo
 */
class GeraTemplate extends Image {

  private $coordNeg = false;
  private $fnSortLinhaColuna;
  private $fnSortLinha;


  /**
   * Cria lista de objetos em {@param arquivo} baseado nas definições de {@param $config}
   * Definição de config
   *  - Definição bloco definir:
   *     - p1 e p2: dois pontos na imagem que formen um diagonal topLeft -> rightBottom
   *     - colunasPorLinha e agrupaObjetos: explicados em {@link gerarBlocos()}
   *     - minArea e maxArea: área mínima e máxima para considerar como um objeto da região 
   *     - id: string ou function especifica um identificador unico para cada regiao encontrada
   *     - tipo: possíveis tipos: elipse, ...
   *          - para tipo elipse:
   *                - casoTrue: string ou function valor retornado caso elipse esteja preenchida
   *                - casoFalse: string ou function valor retornado caso elipse não esteja preenchida
   *
   */
  public function gerarTemplate($arquivo,$config,$resolucao=300){
    $this->init($arquivo,$resolucao);

    $regioes = [];
    foreach ($config['regioes'] as $cb) { # Configuracao Bloco
      $blocos = $this->gerarBlocos($cb);
      $regioes = array_merge($regioes,$this->formataRegioes($cb,$blocos));
    }

    $this->criaArquivoTemplate($config,$regioes);

    $this->criaImagensDebug($regioes);
  }  

  /**
   * Geração do arquivo que será utilizado para interpretação das images,
   * arquivo possui as coordendas de cada um da regiões interpretadas junto
   * junto com a fomatação da saída e os valores necessários para interpretação
   * da folha. 
   */
  private function criaArquivoTemplate($config,$regioes){
    # TODO:
  }

  /**
   * Imagens para visualização do resultado da interpretação
   */
  private function criaImagensDebug($regioes){
    # Posições dos objetos e seus labels
    $copia = Helper::copia($this->image);
    $corTex = imagecolorallocate($copia,0,150,255);
    $corObj = imagecolorallocatealpha ($copia,150,255,0,50);
    foreach ($regioes as $id => $r) {
      imagefilledellipse($copia,$r[1]*$this->escala,$r[2]*$this->escala,30,30,$corObj);
      imagettftext ($copia,17.0,0.0,($r[1]*$this->escala)-5,($r[2]*$this->escala)+5,$corTex,__DIR__.'/SIXTY.TTF',$id);
    }
    imagejpeg($copia,__DIR__.'/../data/runtime/idsRegioes.jpg');
    imagedestroy($this->image);
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
  private function formataRegioes($cb,$blocos){
    $regioes = [];
    foreach ($blocos as $cBloco => $lista) {
      foreach ($lista as $cLinha => $l) {
        $count = 0;
        foreach ($l as $cObjeto => $c) {
          $ancBase = $this->getAncoraMaisProx($c);
          $base = $this->ancoras[$ancBase]->getCentro();
          
          $x = (($c[0])/$this->escala); # Converte para milimetros
          $y = (($c[1])/$this->escala); # Converte para milimetros

          $genId = $cb['id'];
          $idRegiao = is_string($genId) ? $genId : $genId($cBloco,$cLinha,$cObjeto);
          $regioes[$idRegiao] = $this->formataTipoRegiao($cb,$x,$y,$cBloco,$cLinha,$cObjeto);
          $count++;
        }
      }
    }
    return $regioes;
  }

  /**
   * Monta lisda com parâmetros da região de acordo com seu tipo.
   */
  private function formataTipoRegiao($cb,$x,$y,$cBloco,$cLinha,$cObjeto){
    $tipo = $cb['tipo'];

    $regiao = [$tipo,$x,$y];

    if($tipo == 0){ # elipse
      $casoTrue = $cb['casoTrue'];
      $casoFalse = $cb['casoFalse'];
      $regiao[] = is_string($casoTrue) ? $casoTrue : $casoTrue($cBloco,$cLinha,$cObjeto);
      $regiao[] = is_string($casoFalse) ? $casoFalse : $casoFalse($cBloco,$cLinha,$cObjeto);
    }

    return $regiao;
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
   *    L0: A A A
   *    L1: A A A
   * B1:
   *    L0: b b b
   *    L1: B B B
   */
  private function gerarBlocos($cb){
    $pontos = $this->buscador->getPontosDeQuadrado($this->image, $cb['p1'][0],  $cb['p1'][1], $cb['p2'][0],  $cb['p2'][1]);
    $objetos = array_map(function($i){ 
      return $i->getCentro(); 
    },$this->buscador->separaObjetos($pontos, $cb['minArea'], $cb['maxArea']));
    #Helper::pintaObjetos($this->image, $objetos);
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
        foreach ($b as $k3 => $a) {
        }
      }
    }
    return $blocos;
  }

  /**
   * Define resolução, busca âncoras e instancia função de ordenamento
   */
  private function init($arquivo,$resolucao){
      ini_set('memory_limit', '2048M');
      $this->inicializar($arquivo,$resolucao);
      $this->localizarAncoras();
      $this->fnSortLinhaColuna = function($a,$b){ 
        return $a[1] == $b[1] ? $a[0] >= $b[0] : $a[1] >= $b[1];
      };
      $this->fnSortLinha = function($a,$b){
        return $a[0] >= $b[0];
      };
  }

  private function getAncoraMaisProx($ponto){
    if($this->coordNeg){ 
      # TODO: 
      # Depende também do script de processamento reconhecer coordenadas negativas
      # O objetivo do uso de coordenadas negativas é reduzir a distância entre o
      # ponto avaliado e o ponto de referência (âncora) visando diminutir o erro 
      # gerado a partir da conversão entre dpi -> pixel/mm

      // $a1 = $this->ancoras[1]->getCentro(); 
      // $a2 = $this->ancoras[2]->getCentro();
      // $a3 = $this->ancoras[3]->getCentro();
      // $a4 = $this->ancoras[4]->getCentro();
      // $ancoras = [$a1,$a2,$a3,$a4];
      // $dists = array_map(function($i) use($ponto){
      //   return Helper::dist($ponto,$i);
      // },$ancoras);
      // $indice = array_keys($dists, min($dists));
      // return $indice[0]+1;
    } else {
      return 1;
    }

  }


}
