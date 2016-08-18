<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/Image.php');
class ProcessaCommand extends CConsoleCommand {

	public $pid;
	public $dirBase;
	public $dirInBase;
	public $dirIn;
	public $dirOut;
	public $trabalho;

	public $dirDoneFile; # Diretório onde arquivos de log serão salvos

	public function __construct(){
		$this->dirBase = __DIR__ . '/../../../data/runtime';
	}

	public function actionIndex($dirIn=false,$dirOut=false,$trabId=false){
		$this->criaValidaDiretorios($dirIn,$dirOut,$trabId);
		
		$files = array_filter(scandir($this->dirIn),function($i) { 
		  return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; 
		});

		$count = 0;
		$first = true;
		foreach ($files as $i => $f) {
		  	$count++;
		  	if($first || $count % 10 == 0){
		  		$this->trabalho = Trabalho::model()->findByPk($trabId);
		  		$this->log('status atualizado para '. $this->trabalho->status);
			}

		 	$template = $this->trabalho->template;
	     	$arquivo = $this->dirIn.'/'.$f;
	   	 	$arquivoDest = $this->dirOut.'/'.$f;

			if($this->trabalho->status == 1){
				$start = time();
				try {
					$this->log('Processando ' . $f);
					$image = new Image($template,$this->trabalho->taxaPreenchimento);
					$image->exec($arquivo);
					$image->output['arquivo'] = $arquivoDest; # TODO: é usado no debug?
					$imageOutPut = $image->output;
					$this->log('Processamento realizado ' . $f);
				} catch(Exception $e){
  			 	  $this->log('Erro ao processamento ' . $f);
				  $imageOutPut = $e->getMessage();
				}
				#$this->export($f,$imageOutPut);
			}
			$ok = rename($arquivo,$arquivoDest);

			if($this->trabalho->status == 1){ // Trabalho executando | folha interpretada
			  $qtd = Distribuido::model()->updateAll([
			  	'status'=>2,	
			  	'dataFechamento'=>time(),	  	
			  	'output'=>json_encode($imageOutPut),	
			  ],[
			  	'condition'=>"trabalho_id={$this->trabalho->id} AND nome='{$f}'",
			  ]);
			} else {  # | folha somente renomeada
			  $qtd = Distribuido::model()->updateAll([
			  	'status'=>3,	
			  	'nome'=>$f . ' (canelada em ' . date('d/m/Y H:i:s') . ')',	
			  	'dataFechamento'=>time(),	  
			  ],[
			  	'condition'=>"trabalho_id={$this->trabalho->id} AND nome='{$f}'",
			  ]);
			}

		 	$this->log(($ok ? 'OK':'FALHA') . ' | Renomando de {$arquivo} para {$arquivoDest} ');
		}

  		Processo::model()->updateAll([
			'status'=>2,
			'dataFim'=>time(),
		],"trabalho_id={$trabId} AND pid={$this->pid}");

		rmdir($this->dirIn);

		$this->log('Finalizado');
	}
	
	private function criaValidaDiretorios($dirIn,$dirOut,$trabId)
	{
		if(!$dirIn) die("Informe um diretorio de trabalho.\n");
		if(!$dirOut) die("Informe um diretorio de origem.\n");
		if(!$trabId) die("Qual o trabID ?.\n");
		$this->dirInBase = $dirIn;

		$this->dirIn .= $this->dirBase . '/trab-'.$trabId.'/exec/ready/' . $dirIn;
		$this->dirOut = $dirOut;
		if(!is_dir($this->dirBase))	die("Diretorio de trabalho nao encontrado.\n");

		$this->pid = getmypid();

		$this->dirDoneFile = $this->dirBase.'/trab-'.$trabId.'/file';
		if(!is_dir($this->dirDoneFile)) mkdir($this->dirDoneFile,0777);
	}

	private function export($f,$imageOutPut)
	{	
		$output = json_encode($imageOutPut);
		$export = fopen($this->dirDoneFile.'/'.$f.'.json','w');
		fwrite($export,$output);
		fclose($export);
	}

	private function log($msg){
		Yii::log($msg,'trace','tarsius.processa.T'.$this->trabalho->id.'.'.$this->dirInBase);
	}

}

