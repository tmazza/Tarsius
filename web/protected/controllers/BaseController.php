<?php
class BaseController extends CController {

	public $wb;
	public $menu = [];

	protected function beforeAction($action){
		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->wb = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.webroot'));
		return parent::beforeAction($action);
	}

	protected function getTemplate(){
		$templatesDir = Yii::app()->params['templatesDir'];
		
		$diretorios = array_filter(scandir($templatesDir),function($i) use($templatesDir) {
			return strlen($i) > 2 && is_dir($templatesDir.'/'.$i); 
		});

		$valores = array_map(function($i){ 
			return pathinfo(basename($i),PATHINFO_FILENAME); 
		},$diretorios);
		return array_combine($valores,$valores);
	}
}