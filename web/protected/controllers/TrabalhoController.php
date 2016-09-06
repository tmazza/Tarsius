<?php
class TrabalhoController extends BaseController {

	public function actionIndex(){
	    $trabalhos = Trabalho::model()->findAll();
	    $this->render('index',[
	      'trabalhos'=>$trabalhos,
	    ]);
	}

	public function actionNovo(){
		$model = new Trabalho();

		if(isset($_POST['Trabalho'])){
			$model->attributes = $_POST['Trabalho'];
			if($model->validate()){
				$model->save();
				$this->redirect($this->createUrl('/trabalho/index'));
			}
		}
		$this->render('form',[
			'model'=>$model,
			'templates' => $this->getTemplate(),

		]);
	}

	public function actionVer($id){
		$this->render('ver',$this->getInfoTrabalho($id));
	}

	public function actionEditar($id){
		$model = Trabalho::model()->findByPk((int)$id);

		if(isset($_POST['Trabalho'])){
			$model->attributes = $_POST['Trabalho'];
			if($model->validate()){
				$model->save();
				$this->redirect($this->createUrl('/trabalho/index'));
			}
		}

		$this->render('form',[
			'model'=>$model,
			'templates' => $this->getTemplate(),
		]);
	}

	public function actionNaoDistribuidas($id){
		Yii::app()->clientScript->registerScriptFile($this->wb.'/jquery.elevatezoom.min.js');
		$model = Trabalho::model()->findByPk((int)$id);
		$naoDistribuidas = Distribuido::model()->findAll([
			'alias' => 'd',
			'with' => [
				'resultado' => [
					'alias'=>'f',
					'condition' => 'f.exportado=0',
				],
			],
			//'join'=>'JOIN finalizado f ON f.trabalho_id = d.trabalho_id AND f.nome = f.nome',
			'condition'=>"d.trabalho_id={$model->id}",
			'limit'=>20,
		]);

		$this->render('naoDistribuidas',[
			'trabalho'=>$model,
			'naoDistribuidas'=>$naoDistribuidas,
		]);
	}

	private function getInfoTrabalho($id)
	{		
		$trabalho = Trabalho::model()->findByPk((int)$id);
		$qtdDistribuida = Yii::app()->db->createCommand()
				->select('count(*)')
				->from('distribuido')
				->where('trabalho_id = ' . $id . ' AND status = 1')
				->queryColumn();

		$qtdFinalizada = Yii::app()->db->createCommand()
				->select('count(*)')
				->from('finalizado')
				->where('trabalho_id = ' . $id)
				->queryColumn();

		$processosAtivos = Yii::app()->db->createCommand()
				->select('*')
				->from('processo')
				->where('trabalho_id = ' . $id . ' AND status=1')
				->queryAll();

		$naoExportadas = Yii::app()->db->createCommand()
				->select('count(*)')
				->from('finalizado')
				->where('trabalho_id = ' . $id . ' AND exportado=0')
				->queryColumn();

		return [
			'trabalho' => $trabalho,
		 	'distribuido' => array_shift($qtdDistribuida),
		 	'processado' => array_shift($qtdFinalizada),
		 	'processosAtivos' => $processosAtivos,
		 	'naoExportadas' => array_shift($naoExportadas),
		 ];
	}

	public function actionIniciar($id){
		$trabalho = Trabalho::model()->findByPk((int)$id);

		$trabalho->status = 1;
		$trabalho->update(['status']);

		$cmd = 'php ' . Yii::getPathOfAlias('webroot') . '/protected/yiic distribui --trabId=' . $trabalho->id;
		$pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');

		$this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));
	}

	public function actionPausar($id){
		$trabalho = Trabalho::model()->findByPk((int)$id);
		$trabalho->status = 2;
		$trabalho->update(['status']);
		$this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));
	}

	public function actionExcluir($id){
		$trabalho = Trabalho::model()->findByPk((int)$id);
		$trabalho->solicitaPausaProcessos();

		// Aguarda até que todos os arquivos sejam devolvidos para sourceDir
		while($trabalho->qtdProcessosAtivos() > 0)
			sleep(1);

		Processo::model()->deleteAll("trabalho_id = {$trabalho->id}");
		Distribuido::model()->deleteAll("trabalho_id = {$trabalho->id}");
		Finalizado::model()->deleteAll("trabalho_id = {$trabalho->id}");

		$this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));

	}

	public function actionUpdateVer($id){
		$this->renderPartial('_ver',$this->getInfoTrabalho($id));
	}

	public function actionFinalizadas($id){
		$this->render('finalizadas',[
			'trabalho'=>Trabalho::model()->findByPk((int)$id),
		]);		
	}

	private function runDistribui($trabalho) {
	    $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
	    $runner = new CConsoleCommandRunner();
	    $runner->addCommands($commandPath);
	    $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
	    $runner->addCommands($commandPath);
	    $args = array('yiic', 'distribui', '--trabId='.$trabalho->id);
	    $runner->run($args);
	}

	  public function actionExportaResultado($id){
	    $finalizadas = Finalizado::model()->findAll([
	      'condition'=>"trabalho_id=$id AND exportado=0 AND conteudo IS NOT NULL",
	      'limit'=>1024,
	    ]);
	    foreach ($finalizadas as $f) {
	       $conteudo = json_decode($f->conteudo,true);
	       if(isset($conteudo['saidaFormatada'])){
	        $this->export($f,$conteudo['saidaFormatada'],basename($conteudo['arquivo']));
	      }
	    }
	    $qtd = count($finalizadas);
	    HView::fMsg($qtd . HView::plural('exportado',$qtd));
	    $this->redirect($this->createUrl('/trabalho/ver',[
	    	'id'=>$id,
	    ]));
	  }
	
		public function actionForcaExport($id){
			$model = Distribuido::model()->findByPk((int)$id);
			$output = json_decode($model->resultado->conteudo,true);
	        if(isset($output['saidaFormatada'])) {
	        	$this->export($model->resultado,$output['saidaFormatada'],$model->nome);
	        	Yii::app()->user->setFlash('success','Export realizado.');
	        } else {
	        	Yii::app()->user->setFlash('error','Falha ao exportar.');
	        }
	        $this->redirect($this->createUrl('/trabalho/naoDistribuidas',[
	        	'id'=>$model->trabalho_id,
	        ]));
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
	        $model->NomeArquivo = substr($NomeArquivo, 0,-4);
	        $model->attributes = $export;

	        if($model->validate()){
	          if($model->save()){
	            $controleExportada->exportado=1;
	            $controleExportada->update(['exportado']);
	          }
	        } else {
	          HView::fMsg(json_encode($model->getErrors()));
	        }
	      } catch(Exception $e){
	      	HView::fMsg($e->getMessage());
	      }
	  }


}