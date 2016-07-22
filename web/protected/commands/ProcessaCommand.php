<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/Image.php');
class ProcessaCommand extends CConsoleCommand {

	public $pid;
	public $dirBase;
	public $dirIn;
	public $dirOut;
	public $trabalho;

	public function __construct(){
		$this->dirBase = __DIR__ . '/../..';
	}

	public function actionIndex($dirIn=false,$dirOut=false,$trabId=false){

		// echo '***' . Yii::getPathOfAlias('webroot') . '/../../src/*' . "\n";
		// exit;


		if(!$dirIn) die("Informe um diretorio de trabalho.\n");
		if(!$dirOut) die("Informe um diretorio de origem.\n");
		if(!$trabId) die("Qual o trabID ?.\n");
		$this->dirIn .= $this->dirBase . '/exec/ready/' . $dirIn;
		$this->dirOut = $dirOut;
		if(!is_dir($this->dirBase))	die("Diretorio de trabalho nao encontrado.\n");

		$this->pid = getmypid();

		$dirDone = $this->dirBase.'/done';
		if(!is_dir($dirDone)) mkdir($dirDone);

		$dirDoneFile = $this->dirBase.'/done/file';
		if(!is_dir($dirDoneFile)) mkdir($dirDoneFile);

		$files = array_filter(scandir($this->dirIn),function($i) { 
		  return pathinfo($i, PATHINFO_EXTENSION) == 'jpg'; 
		});

		$count = 0;
		$first = true;
		foreach ($files as $i => $f) {
		  $count++;
		  if($first || $count % 50 == 0){
		  	$this->trabalho = Trabalho::model()->findByPk($trabId);
		  }


	      $arquivo = $this->dirIn.'/'.$f;
	   	 $arquivoDest = $this->dirOut.'/'.$f;


		  if($this->trabalho->status == 1){
		    $start = time();

 
		    $template = 'FAURGS_100'; // TODO: pegar do trabalho!

		    try {
		      $image = new Image($template);
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
		    }

		    
		    // salva debug do arquivo
		    $export = fopen($dirDoneFile.'/'.$f.'.json','w');
		    fwrite($export,json_encode($imageOutPut));
		    fclose($export);
		  }

		  // move imagem para pasta de finalizadas
		  rename($arquivo,$arquivoDest);

	
		  $qtd = Distribuido::model()->updateAll([
		  	'status'=>$this->trabalho->status == 1 ? 2 : 3,		  	
		  ],[
		  	'condition'=>"trabalho_id={$this->trabalho->id} AND nome='{$f}'",
		  ]);

		}

  		// UPDATE processo SET status = :status WHERE trabalho_id = :trabId AND pid = :pid');
		$qtd = Processo::model()->updateAll([
			'status'=>2,
		],"trabalho_id={$trabId} AND pid={$this->pid}");

		rmdir ($this->dirIn);

		echo "ok\n";
	}

}

