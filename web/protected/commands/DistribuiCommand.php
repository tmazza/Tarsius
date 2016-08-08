<?php
class DistribuiCommand extends CConsoleCommand
{

  private $qtdProcessadores = 1;
  private $qtdProcessos = 1;
  private $tamMaxBlocoPorProcesso = 250;

  private $trabalho;
  private $dirBase;
  private $dirExec;
  private $dirReady;


  public function __construct(){
    # Define número de processadores da máquina
    $processadores = (int) exec("nproc");
    if ($processadores <= 0)
      echo "\tNumero de processadores nao pode ser identificado\n";
    else
      $this->qtdProcessadores = $processadores;      
    # Quantidade máxima de processos
    $this->qtdProcessos = $this->qtdProcessadores + ceil(0.10*$processadores);

    $this->dirBase = __DIR__ . '/../../../data/runtime';

  }

  public function actionIndex($trabId=false){
    if($trabId){
      $this->setTrabalho($trabId);  

      # Diretório de trabalho
      if (!is_dir($this->dirBase . '/trab-'.$trabId)) mkdir($this->dirBase . '/trab-'.$trabId,0777);

      $this->dirExec = $this->dirBase . '/trab-'.$trabId.'/exec';
      if (!is_dir($this->dirExec)) mkdir($this->dirExec,0777);

      $this->dirReady = $this->dirExec . '/ready';
      if (!is_dir($this->dirReady)) mkdir($this->dirReady,0777);

      $this->loop();
      $this->trabalho->status = 0;
      $this->trabalho->update(['status']); # para trabalho

    } else {
      echo "Qual o trabalho?\n";
    }
  }

  private function loop(){
    do {
      $processosNaoFinalizados = $this->trabalho->qtdProcessosAtivos();

      $files = array_filter(scandir($this->trabalho->sourceDir), function($i){
        return pathinfo($i, PATHINFO_EXTENSION) == 'jpg';
      });

      $files = array_diff($files, $this->trabalho->getJaDistribuidos());
      $processosParaCriar = $this->qtdProcessos - $processosNaoFinalizados;   
      $qtdArquivos = count($files);

      if ($processosParaCriar > 0 && $qtdArquivos > 0) {

          $tamBlocoPorProcesso = ceil($qtdArquivos / $processosParaCriar);
          if ($tamBlocoPorProcesso > $this->tamMaxBlocoPorProcesso)
              $tamBlocoPorProcesso = $this->tamMaxBlocoPorProcesso;

          $files = array_slice($files, 0, $tamBlocoPorProcesso * $processosParaCriar); # Limita quantidade processada
          $tamBloco = ceil($qtdArquivos / $processosParaCriar); # quebra em n pedacos de acordo com o tamnho da entrada
          $blocos = array_chunk($files, $tamBloco);
          
          echo str_pad($qtdArquivos, 5,' ', STR_PAD_LEFT) . ' imagens. ' . 
               str_pad(count($blocos), 3,' ', STR_PAD_LEFT) . ' blocos de ' . 
               str_pad($tamBloco, 3,' ', STR_PAD_LEFT) . ' | ';

          foreach ($blocos as $i => $bloco) {
            $dirHash = hash('crc32', microtime(true) . rand(0, 999999)); # workdir do processo
            $dirDest = $this->dirExec . '/' . $dirHash . '/';
            mkdir($dirDest); # temporario enquanto busca imagens de sourceDir
            foreach ($bloco as $file) {
              if (rename($this->trabalho->sourceDir . '/' . $file, $dirDest . $file)) 
                $this->setJaDistribuido($file,$dirHash);
              else
                echo "Falha ao mover arquivo: {$file} \n";
            }

            rename($dirDest, $this->dirReady . '/' . $dirHash); # Diretorio final após buscar todas imagens do processo
            $cmd = 'hhvm ' . Yii::getPathOfAlias('application') .'/yiic processa';
            $cmd .= " --dirIn={$dirHash}";
            $cmd .= " --dirOut={$this->trabalho->sourceDir}";
            $cmd .= " --trabId={$this->trabalho->id}";

            echo $cmd . "\n";
            $pid = $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');
            $this->criarProcesso($pid,$dirHash,count($bloco));

            echo ($i + 1) . ' ';
          }
          echo "\n";
      }

      $this->trabalho = Trabalho::model()->findByPk($this->trabalho->id);


      echo "\r" . 'Aguardando...';
      sleep($this->trabalho->tempoDistribuicao);

      $this->exportaResultados();
    } while ($this->trabalho->status == 1);
  }

  private function criarProcesso($pid,$dirHash,$qtdArquivos){
    $model = new Processo();
    $model->status = 1;
    $model->pid = $pid;
    $model->trabalho_id = $this->trabalho->id;
    $model->workDir = $dirHash;
    $model->qtd = $qtdArquivos;
    $model->dataInicio = time();
    $model->save();
  }

  private function setJaDistribuido($file,$dirHash){
    $model = new Distribuido();
    $model->nome = $file;
    $model->status = 1;
    $model->trabalho_id = $this->trabalho->id;
    $model->tempDir = $dirHash;
    $model->dataDistribuicao = time();
    $model->save();
  }

  private function setTrabalho($id){
    $this->trabalho = Trabalho::model()->findByPk((int)$id);
    if(is_null($this->trabalho->sourceDir))
      die("\t ***Diretório de trabalho não definido.\n");
    if(!is_dir($this->trabalho->sourceDir))
      die("\t ***Diretório de trabalho não existe.\n");
  }

  private function exportaResultados(){
    $distribuidas = Distribuido::model()->findAll([
      'condition'=>"trabalho_id={$this->trabalho->id} AND exportado=0 AND output IS NOT NULL",
    ]);
    foreach ($distribuidas as $d) {
       $output = json_decode($d->output,true);
       if(isset($output['saidaFormatada'])){
        print_r($output['saidaFormatada']);
        $this->export($d,$output['saidaFormatada'],basename($output['arquivo']));
      }
    }
  }

  private function export($controleExportada,$valor,$NomeArquivo){
      $export = [
        'Ausente' => 'ausente',
        'RespostasOriginais' => 'respostas',
      ];
      $export = array_map(function($i) use($valor) {
        return $valor[$i];
      },$export);

      try {
        $model = new Leitura;
        $model->NomeArquivo = $NomeArquivo;
        $model->attributes = $export;

        if($model->validate()){
          if($model->save()){
            $controleExportada->exportado=1;
            $controleExportada->update(['exportado']);
          }
        } else {
          print_r($model->getErrors());
        }
      } catch(Exception $e){
        echo $e->getMessage();
      }
  }

}