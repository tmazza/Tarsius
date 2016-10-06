<?php
class DistribuiCommand extends CConsoleCommand
{

  private $qtdProcessadores = 1;
  private $limiteProcessos = 1;
  private $tamMaxBlocoPorProcesso = 80;

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
    $this->limiteProcessos = $this->qtdProcessadores + ceil(0.10*$processadores);
    $this->dirBase = __DIR__ . '/../../../data/runtime';
  }

  public function actionIndex($trabId=false){

    if($trabId){
      Yii::log('Iniciando distribuição T:' . $trabId,'trace','tarsius.distribui');

      try{

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


      } catch(Exception $e) {
        Yii::log($e->getMessage(),'trace','tarsius.distribui');

      }
    } else {
      echo "Qual o trabalho?\n";
    }
  }

  private function loop(){


    do {
      $files = CFileHelper::findFiles($this->trabalho->sourceDir,[
        'fileTypes'=>['jpg'],
        'absolutePaths'=>false,
      ]);
      # desconsidera arquivos que já foram processados
      $files = array_diff($files, $this->trabalho->getJaDistribuidos());

      $porcessosAtivos = $this->trabalho->qtdProcessosAtivos();
      $processosLivres = $this->limiteProcessos - $porcessosAtivos;   
      $qtdArquivos = count($files);
  
      if ($qtdArquivos > 0 && $processosLivres > 0) {

          $this->trabalho->setDistribuindo(1);

          $tamBlocoPorProcesso = ceil($qtdArquivos / $processosLivres);
          if ($tamBlocoPorProcesso > $this->tamMaxBlocoPorProcesso)
              $tamBlocoPorProcesso = $this->tamMaxBlocoPorProcesso;

          $files = array_slice($files, 0, $tamBlocoPorProcesso * $processosLivres); # Limita quantidade processada
          $blocos = array_chunk($files, $tamBlocoPorProcesso);
          
          echo str_pad($qtdArquivos, 5,' ', STR_PAD_LEFT) . ' imagens. ' . 
               str_pad(count($blocos), 3,' ', STR_PAD_LEFT) . ' blocos de ' . 
               str_pad($tamBlocoPorProcesso, 3,' ', STR_PAD_LEFT) . ' | ';

          foreach ($blocos as $i => $bloco) {
            $dirHash = hash('crc32', microtime(true) . rand(0, 999999)); # workdir do processo
            $dirDest = $this->dirExec . '/' . $dirHash . '/';
            mkdir($dirDest); # temporario enquanto busca imagens de sourceDir
            foreach ($bloco as $file) {
              if (rename($this->trabalho->sourceDir . '/' . $file, $dirDest . $file)){
                $this->setJaDistribuido($file,$dirHash);
              } else {
                echo "Falha ao mover arquivo: {$file} \n";
              }
            }

            rename($dirDest, $this->dirReady . '/' . $dirHash); # Diretorio final após buscar todas imagens do processo
            $cmd = 'hhvm ' . Yii::getPathOfAlias('application') .'/tarsius processa';
            $cmd .= " --dirIn={$dirHash}";
            $cmd .= " --dirOut={$this->trabalho->sourceDir}";
            $cmd .= " --trabId={$this->trabalho->id}";

            echo $cmd . "\n";
            $pid = $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');
            $this->criarProcesso($pid,$dirHash,count($bloco));

            echo ($i + 1) . ' ';
          }
          echo "\n";

          $this->trabalho->setDistribuindo(0);
      }

      echo "\r" . 'Aguardando...';
      sleep($this->trabalho->tempoDistribuicao);
      $this->trabalho = Trabalho::model()->findByPk($this->trabalho->id);
    } while ($this->trabalho->status == 1);
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

  private function setTrabalho($id){
    $this->trabalho = Trabalho::model()->findByPk((int)$id);
    if(is_null($this->trabalho->sourceDir))
      die("\t ***Diretório de trabalho não definido.\n");
    if(!is_dir($this->trabalho->sourceDir))
      die("\t ***Diretório de trabalho não existe.\n");
  }


}