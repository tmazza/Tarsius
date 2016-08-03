<?php


class TemplateController extends BaseController {

	private $dirGeracaoTempalte = '/data/gerarTemplate';

	public function actionIndex(){
		$this->render('index',[
			'templates'=>$this->getTemplate(),
		]);
	}

	public function actionCriar(){
		$files = CFileHelper::findFiles(Yii::getPathOfAlias('webroot').'/..'.$this->dirGeracaoTempalte,[
			'fileTypes' => ['jpg'],
		]);

		if(isset($_FILES['file'])){
			$filename = Yii::getPathOfAlias('webroot').'/../'.$this->dirGeracaoTempalte.'/a.jpg';
			rename($_FILES['file']['tmp_name'],$filename);
			chmod($filename,0777);
		}

		$this->render('upload',[
			'files' => $files,
		]);
	}

	public function actionGerar(){
		$this->layout = '//layouts/base';
		$this->render('gerar');
	}


	public function actionProcessar(){
		echo '<pre>';
		print_r(json_decode($_POST['pontos'],true));
		exit;
	}

}