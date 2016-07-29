<?php


class TemplateController extends BaseController {

	public function actionIndex(){
		$this->render('index',[
			'templates'=>$this->getTemplate(),
		]);
	}

	public function actionCriar(){
		include_once(Yii::getPathOfAlias('webroot') . '/../src/GeraTemplate.php');
		$img = Yii::getPathOfAlias('webroot') . '/../data/gerarTemplate/a.jpg';
		$g = new GeraTemplate();
		$config = $this->getConfig();
		$g->gerarTemplate($img,$config);
	}


}