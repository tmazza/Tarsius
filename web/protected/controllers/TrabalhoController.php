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
		$trabalho = Trabalho::model()->findByPk((int)$id);
		$this->render('ver',[
			'trabalho'=>$trabalho,
		]);
	}

}