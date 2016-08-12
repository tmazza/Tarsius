<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/Image.php');
class ProcessaCommand extends CConsoleCommand {

	public $pid;
	public $dirBase;
	public $dirIn;
	public $dirOut;
	public $trabalho;

	public function __construct(){
		$this->dirBase = __DIR__ . '/../../../data/runtime';
	}

	public function actionIndex($dirIn=false,$dirOut=false,$trabId=false){
		if(!$dirIn) die("Informe um diretorio de trabalho.\n");
		if(!$dirOut) die("Informe um diretorio de origem.\n");
		if(!$trabId) die("Qual o trabID ?.\n");

		$this->dirIn .= $this->dirBase . '/trab-'.$trabId.'/exec/ready/' . $dirIn;
		$this->dirOut = $dirOut;
		if(!is_dir($this->dirBase))	die("Diretorio de trabalho nao encontrado.\n");

		$this->pid = getmypid();

		$dirDoneFile = $this->dirBase.'/trab-'.$trabId.'/file';
		if(!is_dir($dirDoneFile)) mkdir($dirDoneFile,0777);

		$files = array_filter(scandir($this->dirIn),function($i) { 
		  return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; 
		});

		$count = 0;
		$first = true;
		foreach ($files as $i => $f) {
		  	$count++;
		  	if($first || $count % 10 == 0){
		  		$this->trabalho = Trabalho::model()->findByPk($trabId);
			}

		 	$template = $this->trabalho->template;

	     	$arquivo = $this->dirIn.'/'.$f;
	   	 	$arquivoDest = $this->dirOut.'/'.$f;


			if($this->trabalho->status == 1){
				$start = time();

				try {
				  $image = new Image($template,$this->trabalho->taxaPreenchimento);
				  $image->exec($arquivo);
				  // altera referencia para o arquivo
				  $image->output['arquivo'] = $arquivoDest;
				  $imageOutPut = $image->output;

				  $presenca = '1'; # TODO: 1 presente - 2 ausente  (ausente = marcada)
				  $str = '';
				  foreach ($image->output['regioes'] as $r) { $str .= $r[0]; }
				  $str = $str;
				  $tempos = $image->getTimes();
				  $tempoExec = $tempos['timeAll'];
				} catch(Exception $e){
				  $presenca = '?';
				  $str = $e->getMessage();
				  $tempoExec = '??';
				  $imageOutPut = ['erro'=>$e->getMessage()];
				  // echo $e->getMessage();
				}
					
				$output = json_encode($imageOutPut);

				// salva debug do arquivo
				$export = fopen($dirDoneFile.'/'.$f.'.json','w');
				fwrite($export,$output);
				fclose($export);
			}


			// move imagem para pasta de finalizadas
		 	rename($arquivo,$arquivoDest);

			if($this->trabalho->status == 1){ // Trabalho executando | folha interpretada
			  $qtd = Distribuido::model()->updateAll([
			  	'status'=>2,	
			  	'dataFechamento'=>time(),	  	
			  	'output'=>$output,	
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

		}

  		// UPDATE processo SET status = :status WHERE trabalho_id = :trabId AND pid = :pid');
		$qtd = Processo::model()->updateAll([
			'status'=>2,
			'dataFim'=>time(),
		],"trabalho_id={$trabId} AND pid={$this->pid}");

		rmdir($this->dirIn);

		echo "ok\n";
	}
	
}

