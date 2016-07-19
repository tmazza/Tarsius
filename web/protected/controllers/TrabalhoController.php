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
		]);
	}

	public function actionVer($id){
		$this->render('ver',$this->getInfoTrabalho($id));
	}

	private function getInfoTrabalho($id){		
		$trabalho = Trabalho::model()->findByPk((int)$id);

		$data = Yii::app()->db->createCommand()
			->select('p.id,d.status,count(*) as qtd')
			->from('processo p ')
			->join('distribuido d','d.tempDir = workDir AND d.status != ' . Trabalho::statusFinalizado)
			->where('p.trabalho_id = ' . $trabalho->id)
			->group('p.id')
			->queryAll();

		$faltaProcessar = [];
		foreach ($data as $d)
			$faltaProcessar[$d['id']] = $d['qtd'];
		return [
			'trabalho'=>$trabalho,
			'faltaProcessar'=>$faltaProcessar,
		];
	}

	public function actionIniciar($id){
		$trabalho = Trabalho::model()->findByPk((int)$id);

		$file = Yii::getPathOfAlias('webroot') . '/distribui.php';

		$cmd = 'php ' . $file . ' ' . $trabalho->id;
		$pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');

		$trabalho->status = 1;
		$trabalho->pid = $pid;
		$trabalho->update(['status']);

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

		$this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));

	}

	public function actionUpdateVer($id){
		$this->renderPartial('_ver',$this->getInfoTrabalho($id));
	}



}