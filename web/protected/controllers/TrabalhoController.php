<?php
class TrabalhoController extends BaseController {

	public function actionIndex(){
	    $trabalhos = Trabalho::model()->findAll([
	    	'order' => 'id DESC',
	    ]);
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

	public function actionNaoDistribuidas($id,$pageSize=8){
		Yii::app()->clientScript->registerScriptFile($this->wb.'/jquery.elevatezoom.min.js');
		$model = Trabalho::model()->findByPk((int)$id);

 		$criteria=new CDbCriteria([
			'alias' => 'd',
			'with' => [
				'resultado' => [
					'alias'=>'f',
					'condition' => 'f.exportado=0',
				],
			],
			//'join'=>'JOIN finalizado f ON f.trabalho_id = d.trabalho_id AND f.nome = f.nome',
			'condition'=>"d.trabalho_id={$model->id} AND status !=  " . Distribuido::StatusReprocessamento,
			'order'=>'d.id DESC',
			// 'limit'=>30,
		]);
 		
    	$count=Distribuido::model()->count($criteria);
    	$pages=new CPagination($count);

    	$pages->pageSize=$pageSize;
    	$pages->applyLimit($criteria);
	    $models=Distribuido::model()->findAll($criteria);

		$this->render('naoDistribuidas',[
			'trabalho'=>$model,
			'naoDistribuidas'=>$models,
			'pages'=>$pages,
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

		$cmd = 'php ' . Yii::getPathOfAlias('webroot') . '/protected/tarsius distribui --trabId=' . $trabalho->id;
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
		while($trabalho->qtdProcessosAtivos() > 0){
			sleep(1);
		}

		# apaga registros
		Processo::model()->deleteAll("trabalho_id = {$trabalho->id}");
		Distribuido::model()->deleteAll("trabalho_id = {$trabalho->id}");
		Finalizado::model()->deleteAll("trabalho_id = {$trabalho->id}");

		# apaga arquivos de imagens de resultado de imagem processada
		$dir = Yii::getPathOfAlias('webroot') . '/../data/runtime/trab-' . $trabalho->id;
		CFileHelper::removeDirectory($dir);

		$this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));

	}

	public function actionUpdateVer($id){
		// $finalizadas = Finalizado::model()->findAll([
	 //      'condition'=>"trabalho_id=$id AND exportado=0 AND conteudo IS NOT NULL",
	 //      'limit'=>8,
	 //    ]);
	 //    foreach ($finalizadas as $f) {
	 //       $conteudo = json_decode($f->conteudo,true);
	 //       if(isset($conteudo['saidaFormatada'])){
	 //        $this->export($id,$f,$conteudo['saidaFormatada'],basename($conteudo['arquivo']));
	 //      }
	 //    }

		$erros = Erro::model()->findAll("trabalho_id = $id");
		if(count($erros) > 0){
			echo CHtml::link('Erros encontrados',$this->createUrl('/trabalho/verErros',[
				'id' => (int) $id,
			])) . '<br>';
		}



		$this->renderPartial('_ver',$this->getInfoTrabalho($id));
	}

	public function actionFinalizadas($id){
		$this->render('finalizadas',[
			'trabalho'=>Trabalho::model()->findByPk((int)$id),
		]);		
	}

	public function actionForcaParada($id)
	{
		$trabalho = Trabalho::model()->findByPk((int)$id);
		$trabalho->status = Trabalho::statusParado;
		$trabalho->distribuindo = 0;
		$trabalho->update(['status','distribuindo']);
		$this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));
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
	      'limit'=>2048,
	    ]);
	    foreach ($finalizadas as $f) {
	       $conteudo = json_decode($f->conteudo,true);
	       if(isset($conteudo['saidaFormatada'])){
	        $this->export($id,$f,$conteudo['saidaFormatada'],basename($conteudo['arquivo']));
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
	        	$this->export($model->trabalho_id, $model->resultado,$output['saidaFormatada'],$model->nome);
	        	Yii::app()->user->setFlash('success','Export realizado.');
	        } else {
	        	Yii::app()->user->setFlash('error','Falha ao exportar.');
	        }
	        $this->redirect($this->createUrl('/trabalho/naoDistribuidas',[
	        	'id'=>$model->trabalho_id,
	        ]));
		}

	  private function export($id,$controleExportada,$valor,$NomeArquivo){
	      try {
			$trabalho = Trabalho::model()->findByPk((int) $id);
			if(is_null($trabalho)){
			  throw new Exception('', 1);
			} else {
				$export = json_decode($trabalho->export,true);
				$export = array_map(function($i) use($valor) {
					return $valor[$i];
				},$export);

				$model = new Leitura();
				$model->NomeArquivo = substr($NomeArquivo, 0,-4);
				$model->attributes = $export;

				if($model->validate()){
				  if($model->save()){
				    $controleExportada->exportado=1;
				    $controleExportada->update(['exportado']);
				  }
				} else {
				  throw new Exception(json_encode($model->getErrors()), 1);
				}
			}
	      } catch(Exception $e){
	        $erro = new Erro;
	        $erro->trabalho_id = $id;
	        $erro->texto = $e->getMessage() . ' | ' . json_encode($e);
	        $erro->read = 0;
	        $erro->save();
	      }
	  }

	  public function actionVerErros($id)
	  {
	  	$erros = Erro::model()->findAll("trabalho_id = $id");
	  	$this->render('erros',[
	  		'erros' => $erros,
	  		'id' => $id,
	  	]);
	  }

	  public function actionDeleteErro($id)
	  {
	  	$model = Erro::model()->findByPk((int) $id);
	  	$trabId = $model->trabalho_id;
	  	$model->delete();
	  	$this->redirect($this->createUrl('/trabalho/verErros',[
	  		'id' => $trabId,
	  	]));
	  }

}