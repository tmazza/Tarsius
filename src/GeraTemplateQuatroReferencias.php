<?php
class GeraTemplateQuatroReferencias extends GeraTemplate
{
  public $refAncoras = 4;

  public function gerarTemplate($arquivo,$config,$resolucao=300)
  {
    $this->init($arquivo,$resolucao);
    $regioes = [];
    foreach ($config['regioes'] as $cb) { # Configuracao Bloco
      $blocos = $this->gerarBlocos($cb);
      $regioes = array_merge($regioes,$this->formataRegioes($cb,$blocos));
    }

    # arquivos de saida (template, debug)
    $baseDir = __DIR__.'/../data/template/' . $config['nome'] . '/';# . strtolower(str_replace(' ','_', $config['nome']));

    $this->criaArquivoTemplate($config,$regioes,$baseDir);
    $this->criaImagensDebug($regioes,$baseDir);
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
  protected function formataRegioes($cb,$blocos){
    $regioes = [];
    foreach ($blocos as $cBloco => $lista) {
      foreach ($lista as $cLinha => $l) {
        $count = 0;
        foreach ($l as $cObjeto => $c) {
        	$ancoras = $this->getAncoras();
      $ancora1 = $ancoras[1]->getCentro();
      $ancora2 = $ancoras[2]->getCentro();
      $ancora3 = $ancoras[3]->getCentro();
			$ancora4 = $ancoras[4]->getCentro();

      $p1 = [($c[0] - $ancora1[0])/$this->escala,($c[1] - $ancora1[1])/$this->escala];
      $p3 = [($c[0] - $ancora3[0])/$this->escala,($c[1] - $ancora3[1])/$this->escala];

      $p2 = [($c[0] - $ancora2[0])/$this->escala,($c[1] - $ancora2[1])/$this->escala];
      $p4 = [($c[0] - $ancora4[0])/$this->escala,($c[1] - $ancora4[1])/$this->escala];

			$genId = $cb['id'];
			$idRegiao = is_string($genId) ? $genId : $genId($cBloco,$cLinha,$cObjeto);
			$regioes[$idRegiao] = $this->formataTipoRegiao($cb,[$p1,$p3],[$p2,$p4],$cBloco,$cLinha,$cObjeto,false);
			$count++;
        }
      }
    }
    return $regioes;
  }

  /**
   * Monta lisda com parâmetros da região de acordo com seu tipo.
   */
  protected function formataTipoRegiao($cb,$p1,$p3,$cBloco,$cLinha,$cObjeto,$closest){
    $tipo = $cb['tipo'];

    $regiao = [$tipo,$p1,$p3];

    if($tipo == 0){ # elipse
      $casoTrue = $cb['casoTrue'];
      $casoFalse = $cb['casoFalse'];
      $regiao[] = is_string($casoTrue) ? $casoTrue : $casoTrue($cBloco,$cLinha,$cObjeto);
      $regiao[] = is_string($casoFalse) ? $casoFalse : $casoFalse($cBloco,$cLinha,$cObjeto);
      $regiao[] = $closest;
    }

    return $regiao;
  }

  /**
   * Imagens para visualização do resultado da interpretação
   */
  protected function criaImagensDebug($regioes,$baseDir){
    # Posições dos objetos e seus labels
    $copia = Helper::copia($this->image);
    $cor1 = imagecolorallocatealpha ($copia,150,255,0,0);
    $cor2 = imagecolorallocatealpha ($copia,150,0,0,255);
    $cor3 = imagecolorallocatealpha ($copia,150,250,250,0);
    $cor4 = imagecolorallocatealpha ($copia,150,0,0,255);
    
    foreach ($regioes as $id => $r) {
  		$ancoras = $this->getAncoras();

      $ancora1 = $ancoras[1]->getCentro();
      $ancora2 = $ancoras[2]->getCentro();
      $ancora3 = $ancoras[3]->getCentro();
  		$ancora4 = $ancoras[4]->getCentro();

      list($p1,$p3) = $r[1];
      list($p2,$p4) = $r[2];

      $x1 = $p1[0]*$this->escala+$ancora1[0];
      $y1 = $p1[1]*$this->escala+$ancora1[1];
      $x3 = $p3[0]*$this->escala+$ancora3[0];
      $y3 = $p3[1]*$this->escala+$ancora3[1];

      $x2 = $p2[0]*$this->escala+$ancora2[0];
      $y2 = $p2[1]*$this->escala+$ancora2[1];
      $x4 = $p4[0]*$this->escala+$ancora4[0];
      $y4 = $p4[1]*$this->escala+$ancora4[1];

  		imagefilledellipse($copia,$x1,$y1,7,7,$cor1);
      imagefilledellipse($copia,$x2,$y2,7,7,$cor2);
      imagefilledellipse($copia,$x3,$y3,7,7,$cor3);
  		imagefilledellipse($copia,$x4,$y4,7,7,$cor4);
  		// imagettftext ($copia,17.0,0.0,$x1+2,$y1+2,$corTex,__DIR__.'/SIXTY.TTF',$id);
    }
    imagejpeg($copia,$baseDir.'/preview.jpg');
    imagedestroy($this->image);
  }


}