<?php
class AvaliacaoController extends BaseController {

	public function actionIndex(){
		$dir = Yii::getPathOfAlias('webroot').'/../data/comparacoes/';

		$files = [];
		if(is_dir($dir)){
			$files = CFileHelper::findFiles($dir,[
				'type' => ['html'],
			]);
		}

		$this->render('index',[
			'files' => $files,
		]);
	}

}